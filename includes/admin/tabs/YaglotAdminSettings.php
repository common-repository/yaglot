<?php

namespace Yaglot\Admin\Tabs;

if (!defined('ABSPATH')) {
    exit;
}

use Carbon_Fields\Field;
use Yaglot\Services\YaglotOptionsService;

class YaglotAdminSettings {

    public static function fields(array $languages = null) {

        $fields[] = Field::make('text', YaglotOptionsService::getOptionName('api_key'), __('API Key', YAGLOT_SLUG))
            ->set_help_text(sprintf(esc_html__('Log in to %s to get your API key.', YAGLOT_SLUG), '<a href="' . esc_attr(YAGLOT_DASHBOARD_URL) . '" target="_blank" rel="noopener noreferrer">' . YAGLOT_NAME . '</a>'));

        if (!empty($languages)) {

            $fields[] = Field::make('select', YaglotOptionsService::getOptionName('original_language'), __('Original language', YAGLOT_SLUG))
                ->add_options($languages)
                ->set_default_value("en")
                ->set_help_text(__('What is the original (current) language of your website?', YAGLOT_SLUG));

            $fields[] = Field::make('multiselect', YaglotOptionsService::getOptionName('target_languages'), __('Target languages', YAGLOT_SLUG))
                ->add_options($languages)
                ->set_help_text(sprintf(esc_html__('Choose languages you want to translate into. Supported languages can be found %shere%s.', YAGLOT_SLUG), '<a href="' . esc_attr(YAGLOT_SITE_URL) . 'getting-started/supported-languages#supported_languages" target="_blank" rel="noopener noreferrer">', '</a>'));

        }

        $fields[] = Field::make('html', 'help-settings')
            ->set_html("<div class=\"yaglot-help\">"
                . sprintf(esc_html__('In this section you will need to enter your API Key. It provides an access to translations. You can get it in your %s account. Also, you need to set original and target languages of your website. You can get more detailed information from %sour documentation%s.', YAGLOT_SLUG), '<a href="' . esc_attr(YAGLOT_DASHBOARD_URL) . '" target="_blank" rel="noopener noreferrer">' . YAGLOT_NAME . '</a>', '<a href="' . esc_attr(YAGLOT_DOCUMENTATION_URL . 'articles/settings') . '" target="_blank" rel="noopener noreferrer">', '</a>')
                . "</div>");

        return $fields;
    }

}