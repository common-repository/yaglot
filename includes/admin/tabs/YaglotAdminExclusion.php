<?php

namespace Yaglot\Admin\Tabs;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Carbon_Fields\Field;
use Yaglot\Services\YaglotOptionsService;

class YaglotAdminExclusion {

    public static function fields() {

        /**
         * @var Field\Complex_Field $excludedPagesField
         */
        $excludedPagesField = Field::make( 'complex', YaglotOptionsService::getOptionName('excluded_pages') );
        $excludedPagesField->set_label(__('Exclude pages from translation.', YAGLOT_SLUG));
        $excludedPagesField->set_help_text(__('Add URL that you want to exclude from translations. You can use regular expression to match multiple URLs.', YAGLOT_SLUG));
        $excludedPagesField->setup_labels([
            'plural_name' => __('Pages', YAGLOT_SLUG),
            'singular_name' => __('Page', YAGLOT_SLUG),
        ]);

        /**
         * @var Field\Complex_Field $excludedSelectorsField
         */
        $excludedSelectorsField = Field::make( 'complex', YaglotOptionsService::getOptionName('excluded_selectors') );
        $excludedSelectorsField->set_label(__('Exclude html elements from translation.', YAGLOT_SLUG));
        $excludedSelectorsField->set_help_text(__('Enter the CSS selector of blocks you don\'t want to translate (like a sidebar, a menu, a paragraph etc...)', YAGLOT_SLUG));
        $excludedSelectorsField->setup_labels([
            'plural_name' => __('Selectors', YAGLOT_SLUG),
            'singular_name' => __('Selector', YAGLOT_SLUG),
        ]);

        return [

            $excludedPagesField->add_fields( [
                Field::make( 'text', 'url' )
                    ->set_required( true )
            ] )->set_header_template( '<%- url %>' ),

            $excludedSelectorsField->add_fields( [
                Field::make( 'text', 'selector' )
                    ->set_required( true )
            ] )->set_header_template( '<%- selector %>' ),

            Field::make('html', 'help-exclusion')
                ->set_html("<div class=\"yaglot-help\">"
                    . sprintf(esc_html__('Here you can exclude pages and HTML elements from translation. You can get more detailed information from %sour documentation%s.', YAGLOT_SLUG), '<a href="' . esc_attr(YAGLOT_DOCUMENTATION_URL . 'articles/exclusion') . '" target="_blank" rel="noopener noreferrer">', '</a>')
                    . "</div>")
        ];
    }

}