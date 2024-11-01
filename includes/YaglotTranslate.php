<?php

namespace Yaglot;

use Yaglot\Admin\YaglotAdmin;
use Yaglot\Client\Api\Enum\BotType;
use Yaglot\Helpers\ServerHelper;
use Yaglot\Helpers\UrlHelper;
use Yaglot\Services\YaglotEmailTranslationService;
use Yaglot\Services\YaglotIntegrationsService;
use Yaglot\Services\YaglotLanguagesService;
use Yaglot\Services\YaglotMenuService;
use Yaglot\Services\YaglotMultisiteService;
use Yaglot\Services\YaglotOptionsService;
use Yaglot\Services\YaglotPageTranslationService;
use Yaglot\Services\YaglotParserService;
use Yaglot\Services\YaglotProjectService;
use Yaglot\Services\YaglotRedirectService;
use Yaglot\Services\YaglotReplaceLinkService;
use Yaglot\Services\YaglotReplaceUrlService;
use Yaglot\Services\YaglotRequestUrlService;
use Yaglot\Services\YaglotShortcodesService;
use Yaglot\Services\YaglotSwitchersService;
use Yaglot\Widgets\YaglotSwitcherWidget;

if (!defined('ABSPATH')) {
    exit;
}

class YaglotTranslate {

    /**
     * @var YaglotTranslate
     */
    private static $instance = null;

    /**
     * @var YaglotAdmin
     */
    private $yaglotAdmin;

    /**
     * @var YaglotMenuService
     */
    private $yaglotMenuService;

    /**
     * @var YaglotPageTranslationService
     */
    private $pageTranslationService;

    /**
     * @var YaglotOptionsService
     */
    private $optionsService;

    /**
     * @var YaglotLanguagesService
     */
    private $languagesService;

    /**
     * @var YaglotProjectService
     */
    private $projectService;

    /**
     * @var YaglotEmailTranslationService
     */
    private $emailTranslationService;

    /**
     * @var YaglotRedirectService
     */
    private $redirectService;

    /**
     * @var YaglotRequestUrlService
     */
    private $requestUrlService;

    /**
     * @var YaglotParserService
     */
    private $parserService;

    /**
     * @var YaglotSwitchersService
     */
    private $switchersService;

    /**
     * @var YaglotReplaceLinkService
     */
    private $replaceLinkService;

    /**
     * @var YaglotMultisiteService
     */
    private $multisiteService;

    /**
     * @var YaglotShortcodesService
     */
    private $shortcodesService;

    /**
     * @var YaglotReplaceUrlService
     */
    private $replaceUrlService;

    /**
     * @var YaglotIntegrationsService
     */
    private $integrationsService;

    /**
     * @var UrlHelper
     */
    private $yaglotUrl;

    /**
     * @var string
     */
    private $originalLanguage;

    /**
     * @var string[]
     */
    private $targetLanguages = [];

    /**
     * @var string[]
     */
    private $excludedUrls = [];

    /**
     * @var string
     */
    private $currentLanguage;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @return YaglotTranslate
     */
    public static function getInstance() {

        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * YaglotTranslate constructor.
     */
    private function __construct() {

        $this->languagesService = new YaglotLanguagesService();
        $this->projectService = new YaglotProjectService();
        $this->optionsService = new YaglotOptionsService();

        $this->yaglotAdmin = new YaglotAdmin(
            $this->optionsService,
            $this->languagesService,
            $this->projectService
        );

        add_action('carbon_fields_fields_registered', [$this, 'init']);
    }

    private function __clone() {
    }

    private function __sleep() {
    }

    private function __wakeup() {
    }

    /**
     * @return void
     */
    public function init() {

        $this->apiKey = $this->optionsService->getApiKey();
        $this->originalLanguage = $this->optionsService->getOriginalLanguage();
        $this->targetLanguages = $this->optionsService->getTargetLanguages();
        $this->excludedUrls = $this->optionsService->getExcludedUrls();

        $this->requestUrlService = new YaglotRequestUrlService(
            $this->optionsService,
            $this->originalLanguage,
            $this->targetLanguages,
            $this->excludedUrls
        );

        $this->currentLanguage = $this->requestUrlService->getCurrentLanguage();
        $this->setCookieLanguage($this->currentLanguage);

        $this->yaglotUrl = $this->requestUrlService->getYaglotUrl();

        $this->switchersService = new YaglotSwitchersService(
            $this->optionsService,
            $this->languagesService,
            $this->yaglotUrl,
            $this->originalLanguage,
            $this->currentLanguage,
            $this->targetLanguages
        );

        $this->yaglotMenuService = new YaglotMenuService(
            $this->optionsService,
            $this->languagesService,
            $this->requestUrlService,
            $this->switchersService
        );

        add_action('widgets_init', [$this, 'registerWidgets']);

        if (is_admin()) {
            return;
        }

        if (!$this->apiKey) {
            return;
        }

        if (!$this->originalLanguage) {
            return;
        }

        if (!$this->currentLanguage) {
            return;
        }

        $this->parserService = new YaglotParserService(
            $this->optionsService,
            $this->requestUrlService
        );

        $this->emailTranslationService = new YaglotEmailTranslationService(
            $this->parserService,
            $this->originalLanguage
        );

        $this->integrationsService = new YaglotIntegrationsService(
            $this->languagesService,
            $this->requestUrlService,
            $this->optionsService,
            $this->originalLanguage,
            $this->currentLanguage
        );

        add_filter('wp_mail', [$this, 'translateEmail'], 10, 1);
        add_action('wp_head', [$this, 'headMeta']);
        if (!$this->requestUrlService->isTranslatableUrl()) {
            return;
        }

        add_action('wp_redirect', [$this, 'redirectHandler']);
        add_action('init', [$this, 'initPageBuffer']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('wp_head', [$this, 'headHrefLangs']);
    }

    /**
     * @return void
     */
    public function prepareRequestUri() {

        $_SERVER['REQUEST_URI'] = str_replace(
            '/' . $this->currentLanguage . '/',
            '/',
            $_SERVER['REQUEST_URI']
        );

        $_SERVER['REQUEST_URI'] = preg_replace("/^\/{$this->currentLanguage}$/", '/', $_SERVER['REQUEST_URI']);
    }

    /**
     * @param string $language
     */
    public function setCookieLanguage($language) {
        setcookie(YAGLOT_PREFIX . 'lang', $language, time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
    }

    /**
     * @return string|null
     */
    public function getCookieLanguage() {
        return isset($_COOKIE[YAGLOT_PREFIX . 'lang']) ? $_COOKIE[YAGLOT_PREFIX . 'lang'] : null;
    }

    /**
     * @return void
     */
    public function enqueueScripts() {

        wp_enqueue_script(YAGLOT_SLUG . '_sdk', YAGLOT_URL . 'assets/js/sdk.js', [], YAGLOT_VERSION, true);

        $script = 'Yaglot = ' . json_encode([
                'apiKey'                   => $this->apiKey,
                'originalLanguage'         => $this->originalLanguage,
                'currentLanguage'          => $this->currentLanguage,
                'targetLanguages'          => $this->targetLanguages,
                'rtl'                      => $this->optionsService->getRtl(),
                'progressBar'              => false,
                'translatePages'           => false,
                'createSwitchers'          => false,
                'browserLanguageDetection' => false,
                'excludedSelectors'        => $this->optionsService->getExcludedBlocks()

            ]) . ';';

        wp_add_inline_script(YAGLOT_SLUG . '_sdk', $script, 'before');
    }

    /**
     * @param string $location
     * @return string
     */
    public function redirectHandler($location) {

        if (!empty($this->getCookieLanguage())
            && $this->currentLanguage !== $this->originalLanguage
            && $this->replaceUrlService->checkLink($location)) {

            return $this->replaceLinkService->replaceUrl($location, $this->currentLanguage);
        }

        return $location;
    }

    /**
     * @return void
     */
    private function checkNeedToRedirect() {

        if (wp_doing_ajax()) {
            return;
        }

        if (ServerHelper::detectBot($_SERVER) !== BotType::HUMAN) {
            return;
        }

        if (!$this->optionsService->getBrowserLanguageDetection()) {
            return;
        }

        $this->redirectService->autoRedirect($this->currentLanguage, $this->getCookieLanguage(), array_merge([$this->originalLanguage], $this->targetLanguages), $this->requestUrlService->getYaglotUrl());
    }

    /**
     * @param array $args
     * @return array
     */
    public function translateEmail($args) {

        if (!$this->optionsService->getTranslateEmails()) {
            return $args;
        }

        $message_and_subject = [
            'subject' => $args['subject'],
            'message' => $args['message'],
        ];

        if ($this->currentLanguage !== $this->originalLanguage) {

            $message_and_subject_translated = $this->emailTranslationService->translate($message_and_subject, $this->currentLanguage);

        } elseif (isset($_SERVER['HTTP_REFERER'])) {

            $url = $this->requestUrlService->createUrlObject($_SERVER['HTTP_REFERER']);
            $choose_current_language = $url->detectCurrentLanguage();

            if ($choose_current_language !== $this->originalLanguage) {

                $message_and_subject_translated = $this->emailTranslationService->translate($message_and_subject, $choose_current_language);

            } elseif (strpos($_SERVER['HTTP_REFERER'], 'language=') !== false) {

                $pos = strpos($_SERVER['HTTP_REFERER'], 'language=');
                $start = $pos + strlen('language=');
                $choose_current_language = substr($_SERVER['HTTP_REFERER'], $start, 2);

                if ($choose_current_language && $choose_current_language !== $this->originalLanguage) {
                    $message_and_subject_translated = $this->emailTranslationService->translate($message_and_subject, $choose_current_language);
                }
            }
        }

        if (!empty($message_and_subject_translated) && strpos($message_and_subject_translated['subject'], '</p>') !== false) {
            $pos = strpos($message_and_subject_translated['subject'], '</p>') + 4;
            $args['subject'] = substr($message_and_subject_translated['subject'], 3, $pos - 7);
            $args['message'] = $message_and_subject_translated['message'];
        }

        return $args;
    }

    /**
     * @return void
     */
    public function initPageBuffer() {

        $this->prepareRequestUri();

        if (!$this->requestUrlService->isEligibleUrl($this->requestUrlService->getFullUrl())) {
            return;
        }

        $this->redirectService = new YaglotRedirectService();
        $this->checkNeedToRedirect();

        $this->multisiteService = new YaglotMultisiteService($this->requestUrlService);
        $this->replaceLinkService = new YaglotReplaceLinkService($this->multisiteService, $this->originalLanguage, $this->currentLanguage);

        $this->replaceUrlService = new YaglotReplaceUrlService(
            $this->requestUrlService,
            $this->replaceLinkService
        );

        $this->shortcodesService = new YaglotShortcodesService(
            $this->switchersService,
            $this->requestUrlService,
            $this->optionsService
        );

        $this->pageTranslationService = new YaglotPageTranslationService(
            $this->optionsService,
            $this->requestUrlService,
            $this->parserService,
            $this->languagesService,
            $this->switchersService,
            $this->replaceLinkService,
            $this->replaceUrlService,
            $this->integrationsService,
            $this->originalLanguage,
            $this->currentLanguage,
            $this->targetLanguages
        );

        ob_start([$this->pageTranslationService, 'handlePageBuffer']);
    }

    /**
     * @return void
     */
    public function headHrefLangs() {
        echo $this->requestUrlService->getYaglotUrl()->generateHrefLangsTags();
    }

    /**
     * @return void
     */
    public function headMeta() {
        echo '<meta property="yg:original_language" content="' . $this->originalLanguage . '">';
    }

    /**
     * @return void
     */
    public function registerWidgets() {
        register_widget(new YaglotSwitcherWidget(
            $this->optionsService,
            $this->switchersService,
            $this->requestUrlService
        ));
    }

    /**
     * @return void
     */
    public function activate() {

        $structure = get_option('permalink_structure');
        if (empty($structure)) {
            add_option(YaglotOptionsService::getOptionName('old_permalink_structure'), $structure);
            update_option('permalink_structure', '/%postname%/');
        }

        flush_rewrite_rules();
    }

    /**
     * @return void
     */
    public function deactivate() {

        $old_structure = get_option(YaglotOptionsService::getOptionName('old_permalink_structure'));
        if ($old_structure) {
            delete_option(YaglotOptionsService::getOptionName('old_permalink_structure'));
            update_option('permalink_structure', $old_structure);
        }

        flush_rewrite_rules();
    }

    /**
     * @return void
     */
    public function uninstall() {

        flush_rewrite_rules();

        $this->optionsService->clear();
    }
}