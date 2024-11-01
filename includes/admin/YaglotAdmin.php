<?php

namespace Yaglot\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Yaglot\Admin\Tabs\YaglotAdminExclusion;
use Yaglot\Admin\Tabs\YaglotAdminOther;
use Yaglot\Admin\Tabs\YaglotAdminSettings;
use Yaglot\Admin\Tabs\YaglotAdminSwitcher;
use Yaglot\Carbon\Yaglot_Theme_Options_Datastore;
use Yaglot\Entities\YaglotKeyInfo;
use Yaglot\Exceptions\ServerErrorException;
use Yaglot\Services\YaglotLanguagesService;
use Yaglot\Services\YaglotOptionsService;
use Yaglot\Services\YaglotProjectService;

class YaglotAdmin {


    /**
     * @var Container\Yaglot_Admin_Page_Container
     */
    private $pageContainer;


    /**
     * @var YaglotLanguagesService
     */
    private $languagesService;


    /**
     * @var YaglotProjectService
     */
    private $projectService;


    /**
     * @var YaglotOptionsService
     */
    private $optionsService;


    /**
     * @var array
     */
    private $errors = [];


    /**
     * YaglotAdmin constructor.
     * @param YaglotOptionsService $optionsService
     * @param YaglotLanguagesService $languagesService
     * @param YaglotProjectService $projectService
     */
    public function __construct(YaglotOptionsService $optionsService,
                                YaglotLanguagesService $languagesService,
                                YaglotProjectService $projectService) {

        if (did_action('init')) {
            return;
        }

        $this->languagesService = $languagesService;
        $this->projectService = $projectService;
        $this->optionsService = $optionsService;

        $this->hooks();
    }


    /**
     * @return void
     */
    private function hooks() {

        add_action('yaglot_boot_carbon', [$this, 'bootCarbon']);
        add_action('after_setup_theme', [$this, 'afterSetupTheme']);
        add_action('carbon_fields_register_fields', [$this, 'registerCarbonFields']);

        if (!is_admin()) {
            return;
        }

        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('admin_notices', [$this, 'handleErrors']);
        add_filter('plugin_action_links_' . plugin_basename(YAGLOT_FILE), [$this, 'pluginActionsLinks']);
    }


    /**
     * @return string
     */
    private function getAdminUrl() {
        return admin_url(sprintf('admin.php?page=%s', $this->pageContainer->get_page_file()));
    }


    /**
     * Add links
     *
     * @see plugin_action_links_WEGLOT_BNAME
     *
     * @param array $links
     * @return array
     */
    public function pluginActionsLinks($links) {

        $url = $this->getAdminUrl();
        $text = __('Settings', YAGLOT_SLUG);

        $links[] = sprintf('<a href="%s">%s</a>', esc_attr($url), $text);

        return $links;
    }


    /**
     * @return YaglotKeyInfo|null
     */
    private function getProjectInfo() {

        $url = admin_url(sprintf('admin.php?page=%s', $this->pageContainer->get_page_file()));

        $apiKey = $this->optionsService->getApiKey();
        $targetLanguages = $this->optionsService->getTargetLanguages();
        if (!$apiKey) {

            $this->errors[] = sprintf(esc_html__('Plugin is installed but not configured yet. You need to configure %s %shere%s. The configuration takes only 1 minute!', YAGLOT_SLUG), YAGLOT_NAME, '<a href="' . esc_attr($url) . '">', '</a>');

            return null;
        }

        if (empty($targetLanguages)) {
            $this->errors[] = sprintf(esc_html__('Plugin is installed but languages are not filled. You can fill them %shere%s.', YAGLOT_SLUG), '<a href="' . esc_attr($url) . '">', '</a>');
        }

        try {

            $projectKeyInfo = $this->projectService->getInfo($apiKey);

        } catch (ServerErrorException $e) {

            $this->errors[] = $e->getMessage();

            return null;
        }


        if ($projectKeyInfo
            && $projectKeyInfo->plan->limit_languages > -1
            && count($targetLanguages) > $projectKeyInfo->plan->limit_languages) {

            $this->errors[] = sprintf(esc_html__("Max %d target languages allowed. Please %supgrade your plan%s to use more languages.", YAGLOT_SLUG), $projectKeyInfo->plan->limit_languages, '<a href="' . esc_attr(sprintf(YAGLOT_BILLING_URL, $projectKeyInfo->account->id)) . '" target="_blank" rel="noopener noreferrer">', '</a>');
        }

        return $projectKeyInfo;
    }

    /**
     * @return void
     */
    public function afterSetupTheme() {
        do_action('yaglot_boot_carbon');
    }

    /**
     * @return void
     */
    public function bootCarbon() {

        if (Carbon_Fields::is_booted()) {
            return;
        }

        Carbon_Fields::boot();
    }

    /**
     * @return void
     */
    public function registerCarbonFields() {

        $this->registerDefaultOptions();

        $this->pageContainer = Container::make('yaglot_admin_page', YAGLOT_NAME);
        $this->pageContainer->set_icon(YAGLOT_URL_IMAGES . "logo.png");
        $this->pageContainer->set_page_file(YAGLOT_SLUG);
        $this->pageContainer->set_datastore(new Yaglot_Theme_Options_Datastore());
        $this->pageContainer->setLanguagesList($this->languagesService->getLanguages());

        $this->pageContainer->add_tab(__('Settings', YAGLOT_SLUG), YaglotAdminSettings::fields($this->pageContainer->getLanguagesList()));
        $this->pageContainer->add_tab(__('Switcher', YAGLOT_SLUG), YaglotAdminSwitcher::fields());
        $this->pageContainer->add_tab(__('Exclusion', YAGLOT_SLUG), YaglotAdminExclusion::fields());
        $this->pageContainer->add_tab(__('Other', YAGLOT_SLUG), YaglotAdminOther::fields());

        if (is_admin()) {
            $this->pageContainer->setProjectInfo($this->getProjectInfo());
        }
    }


    /**
     * @return void
     */
    private function registerDefaultOptions() {

        if (!get_option(YAGLOT_SLUG)) {

            update_option(YAGLOT_SLUG, true);
            update_option(YaglotOptionsService::getOptionName('switcher_selectors|||0|value'), 'switcher');

            foreach (YaglotAdminSwitcher::getDefaultSwitcher(rand(0, 9999999)) as $key => $value) {
                update_option(YaglotOptionsService::getOptionName('switcher_selectors|' . $key . '|0|0|value'), is_bool($value) ? var_export($value, true) : $value);
            }
        }
    }


    /**
     * @return void
     */
    public function handleErrors() {

        if (empty($this->errors)) {
            return;
        }

        foreach ($this->errors as $error) {
            include yaglot_path(['carbon', 'templates', 'yaglot_error.php']);
        }
    }


    /**
     * @return void
     */
    public function enqueueScripts() {

        if ($this->pageContainer
            && $this->pageContainer->should_activate()) {

            wp_enqueue_script(YAGLOT_SLUG . '_admin_script', YAGLOT_URL . 'assets/js/admin.js', [
                'jquery'
            ], YAGLOT_VERSION);

            wp_enqueue_script(YAGLOT_SLUG . '_support', YAGLOT_CDN_URL . 'support.js', [
                'jquery',
            ], YAGLOT_VERSION);

            $script = 'YaglotSupportConfig = ' . json_encode([
                    'formSubject' => YAGLOT_SUPPORT_FORM_SUBJECT,
                    'lang'        => explode('_', get_locale())[0]
                ]) . ';';

            wp_add_inline_script(YAGLOT_SLUG . '_support', $script, 'before');

            wp_enqueue_style(YAGLOT_SLUG . '_sdk', YAGLOT_URL . 'assets/css/sdk.css', [], YAGLOT_VERSION);
            wp_enqueue_style(YAGLOT_SLUG . '_admin', YAGLOT_URL . 'assets/css/admin.css', [], YAGLOT_VERSION);
        }

    }

}