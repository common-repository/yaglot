<?php

namespace Yaglot\Admin\Tabs;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Carbon_Fields\Field;
use Yaglot\Services\YaglotOptionsService;

class YaglotAdminOther {


    public static function fields() {

        return [

            Field::make('checkbox', YaglotOptionsService::getOptionName('translate_emails'), __('Translate wordpress emails', YAGLOT_SLUG))
                ->set_help_text(__('Turn off to disable email translations', YAGLOT_SLUG))
                ->set_default_value('true')
                ->set_option_value('true'),

            Field::make('checkbox', YaglotOptionsService::getOptionName('translate_amp'), __('Translate AMP pages', YAGLOT_SLUG))
                ->set_help_text(__('Turn off to disable AMP pages translations', YAGLOT_SLUG))
                ->set_default_value('true')
                ->set_option_value('true'),

            Field::make('checkbox', YaglotOptionsService::getOptionName('browser_language_detection'), __('Detect language by browser on first visit', YAGLOT_SLUG))
                ->set_help_text(__('Turn off to disable redirection by visitor browser language', YAGLOT_SLUG))
                ->set_default_value('true')
                ->set_option_value('true'),

            Field::make('checkbox', YaglotOptionsService::getOptionName('create_switchers'), __('Show languages switchers on the page', YAGLOT_SLUG))
                ->set_help_text(__('Turn off to hide all switchers', YAGLOT_SLUG))
                ->set_default_value('true')
                ->set_option_value('true'),

            Field::make('checkbox', YaglotOptionsService::getOptionName('rtl'), __('Enable RTL', YAGLOT_SLUG))
                ->set_help_text(sprintf(esc_html__('Add %s to html on RTL languages', YAGLOT_SLUG), '<code>dir="rtl"</code>'))
                ->set_default_value('true')
                ->set_option_value('true'),

            Field::make('html', 'help-other')
                ->set_html("<div class=\"yaglot-help\">"
                    . sprintf(esc_html__('In this section you can enable or disable detecting language by browser, switchers display and RTL. You can get more detailed information from %sour documentation%s.', YAGLOT_SLUG), '<a href="' . esc_attr(YAGLOT_DOCUMENTATION_URL . 'articles/other') . '" target="_blank" rel="noopener noreferrer">', '</a>')
                    . "</div>")

        ];
    }

}