<?php

namespace Carbon_Fields\Container;

use Yaglot\Entities\YaglotKeyInfo;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Yaglot_Admin_Page_Container extends Theme_Options_Container {

    /**
     * @var array
     */
    private $languagesList;


    /**
     * @var YaglotKeyInfo|null
     */
    private $projectKeyInfo;


    /**
     * Yaglot_Admin_Page_Container constructor.
     * @param $id
     * @param $title
     * @param $type
     * @param $condition_collection
     * @param $condition_translator
     */
    public function __construct($id, $title, $type, $condition_collection, $condition_translator) {
        parent::__construct($id, $title, "theme_options", $condition_collection, $condition_translator);
    }


    /**
     * @param array $languages
     */
    public function setLanguagesList($languages) {
        $this->languagesList = $languages;
    }


    /**
     * @return array|null
     */
    public function getLanguagesList() {
        return $this->languagesList;
    }


    /**
     * @param YaglotKeyInfo|null $projectKeyInfo
     */
    public function setProjectInfo(YaglotKeyInfo $projectKeyInfo = null) {
        $this->projectKeyInfo = $projectKeyInfo;
    }


    /**
     * @return YaglotKeyInfo|null
     */
    public function getProjectInfo() {
        return $this->projectKeyInfo;
    }


    /**
     * Output the container markup
     */
    public function render() {

        $input = stripslashes_deep( $_GET );
        $request_settings_updated = isset( $input['settings-updated'] ) ? $input['settings-updated'] : '';
        if ( $request_settings_updated === 'true' ) {
            $this->notifications[] = __( 'Settings saved.', YAGLOT_SLUG );
        }

        include yaglot_path(['carbon', 'templates', 'yaglot_admin_page.php']);
    }

}
