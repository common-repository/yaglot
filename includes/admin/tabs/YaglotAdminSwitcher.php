<?php

namespace Yaglot\Admin\Tabs;

if (!defined('ABSPATH')) {
    exit;
}

use Carbon_Fields\Field;
use Yaglot\Services\YaglotOptionsService;

class YaglotAdminSwitcher {

    const DEFAULT_SWITCHER_POSITION = 'bottom-right';
    const DEFAULT_SWITCHER_TYPE     = 'dropdown';
    const DEFAULT_FLAGS_TYPE        = 'rounded';
    const DEFAULT_SWITCHER_DATA     = 'all';

    public static $positionTypes = [
        'top-left'     => 'Top-Left',
        'top-right'    => 'Top-Right',
        'bottom-left'  => 'Bottom-Left',
        'bottom-right' => 'Bottom-Right',
    ];

    public static $switcherTypes = [
        'dropdown' => 'Dropdown',
        'inline'   => 'Inline',
        'select'   => 'Select',
    ];

    public static $switcherData = [
        'all'    => 'With titles and flags',
        'flags'  => 'Only flags',
        'titles' => 'Only titles',
    ];

    public static $flagsTypes = [
        'rounded'   => 'Rounded',
        'circle'    => 'Circle',
        'square'    => 'Square',
        'rectangle' => 'Rectangle',
    ];


    /**
     * @param int $id
     * @return array
     */
    public static function getDefaultSwitcher($id = null) {

        return [
            'switcher_id'    => $id ? $id : '',
            'switcher_data'  => self::DEFAULT_SWITCHER_DATA,
            'class'          => '',
            'css'            => '',
            'default_styles' => true,
            'short_title'    => false,
            'offset'         => '10px',
            'position'       => self::DEFAULT_SWITCHER_POSITION,
            'fixed_position' => true,
            'flags'          => self::DEFAULT_FLAGS_TYPE,
            'type'           => self::DEFAULT_SWITCHER_TYPE,
        ];
    }


    /**
     * @return array
     */
    public static function fields() {

        /**
         * @var Field\Complex_Field $switcherSelectorsField
         */
        $switcherSelectorsField = Field::make('complex', YaglotOptionsService::getOptionName('switcher_selectors'));
        $switcherSelectorsField->set_label(__('Create your own switchers', YAGLOT_SLUG));
        $switcherSelectorsField->setup_labels([
            'plural_name'   => __('Switchers', YAGLOT_SLUG),
            'singular_name' => __('Switcher', YAGLOT_SLUG),
        ]);

        return [

            $switcherSelectorsField
                ->add_fields('switcher', [

                    Field::make('hidden', 'switcher_id'),

                    Field::make('html', 'preview')
                        ->set_html('<div class="switcher-preview-container"></div>'),

                    Field::make('select', 'type', __('Type', YAGLOT_SLUG))
                        ->add_options(self::$switcherTypes)
                        ->set_default_value(self::DEFAULT_SWITCHER_TYPE),

                    Field::make('select', 'flags', __('Flags', YAGLOT_SLUG))
                        ->add_options(self::$flagsTypes)
                        ->set_default_value(self::DEFAULT_FLAGS_TYPE),

                    Field::make('radio', 'switcher_data')
                        ->add_options(self::$switcherData)
                        ->set_default_value(self::DEFAULT_SWITCHER_DATA),

                    Field::make('checkbox', 'fixed_position', __('Insert switcher in the page corner', YAGLOT_SLUG))
                        ->set_help_text(__("If you enable this option, the switcher will be automatically placed in the page corner. If you want to set switcher's position yourself, disable this option.", YAGLOT_SLUG))
                        ->set_option_value('true')
                        ->set_default_value('true'),

                    Field::make('select', 'positon', __('Position', YAGLOT_SLUG))
                        ->add_options(self::$positionTypes)
                        ->set_conditional_logic(array(
                            'relation' => 'AND',
                            array(
                                'field'   => 'fixed_position',
                                'value'   => true,
                                'compare' => '=',
                            )
                        ))
                        ->set_default_value(self::DEFAULT_SWITCHER_POSITION),

                    Field::make('text', 'offset', __('Offset from corner', YAGLOT_SLUG))
                        ->set_attribute('placeholder', "e.g.: 10px")
                        ->set_default_value("10px")
                        ->set_conditional_logic(array(
                            'relation' => 'AND',
                            array(
                                'field'   => 'fixed_position',
                                'value'   => true,
                                'compare' => '=',
                            )
                        )),


                    Field::make('checkbox', 'default_styles', __('Use predesigned css for switcher', YAGLOT_SLUG))
                        ->set_option_value('true')
                        ->set_default_value('true'),

                    Field::make('checkbox', 'short_title', __('Show languages codes as titles', YAGLOT_SLUG))
                        ->set_option_value('true'),

                    Field::make('text', 'class', __('Class', YAGLOT_SLUG))
                        ->set_help_text(__('Assign additional classes to switcher.', YAGLOT_SLUG)),

                    Field::make('textarea', 'css', __('Custom CSS', YAGLOT_SLUG))
                        ->set_attribute('placeholder', "e.g.: {{id}} a:hover { font-weight: bold; }")
                        ->set_help_text(__('Write own CSS styles. Use <code>{{id}}</code> to generate unique selector to this switcher.', YAGLOT_SLUG))
                        ->set_rows(4),

                    Field::make('html', 'shortcode')
                        ->set_html('<label for="yg-switcher-shortcode">' . __('Shortcode', YAGLOT_SLUG) . '</label><input id="yg-switcher-shortcode" type="text" class="switcher-shortcode" readonly="readonly">')
                        ->set_help_text(__('Use this shortcode to append switcher manually.', YAGLOT_SLUG))
                ])->set_header_template( '#<%- switcher_id %> - <%- type %>' ),

            Field::make('html', 'help-switcher')
                ->set_html("<div class=\"yaglot-help\">"
                    . sprintf(esc_html__('In the "Switcher" section you are able to clone, delete, add new or edit existing switchers. Different options are available for customization. For example, you can change switcher’s type, data, position, offset, etc. You can get more detailed information from %sour documentation%s.', YAGLOT_SLUG), '<a href="' . esc_attr(YAGLOT_DOCUMENTATION_URL . 'categories/switcher') . '" target="_blank" rel="noopener noreferrer">', '</a>')
                    . "</div>")
        ];
    }

}