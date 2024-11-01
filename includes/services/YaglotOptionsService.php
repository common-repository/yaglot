<?php

namespace Yaglot\Services;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class YaglotOptionsService {

    public static $options = [
        'original_language',
        'api_key',
        'target_languages',
        'excluded_pages',
        'excluded_selectors',
        'switcher_selectors',
        'switcher_selectors',
        'translate_emails',
        'translate_amp',
        'browser_language_detection',
        'create_switchers',
        'rtl'
    ];

    /**
     * @param string $key
     * @return string
     */
    public static function getOptionName($key) {
        return YAGLOT_PREFIX . $key;
    }


    /**
     * @return string|null
     */
    public function getOriginalLanguage() {
        return carbon_get_theme_option(self::getOptionName('original_language'));
    }


    /**
     * @return string|null
     */
    public function getApiKey() {
        return carbon_get_theme_option(self::getOptionName('api_key'));
    }


    /**
     * @return array
     */
    public function getTargetLanguages() {
        return carbon_get_theme_option(self::getOptionName('target_languages'));
    }


    /**
     * @return array
     */
    public function getExcludedUrls() {

        $urls = array_column(carbon_get_theme_option(self::getOptionName('excluded_pages')), 'url');
        $urls[] = '/wp-login.php';

        return $urls;
    }


    /**
     * @return array
     */
    public function getExcludedBlocks() {

        $blocks = array_column(carbon_get_theme_option(self::getOptionName('excluded_selectors')), 'selector');
        $blocks[] = '#wpadminbar';

        return $blocks;
    }


    /**
     * @return array
     */
    public function getSwitchers() {
        return carbon_get_theme_option(self::getOptionName('switcher_selectors'));
    }


    /**
     * @return boolean
     */
    public function getTranslateEmails() {
        return carbon_get_theme_option(self::getOptionName('translate_emails'));
    }


    /**
     * @return boolean
     */
    public function getTranslateAmp() {
        return carbon_get_theme_option(self::getOptionName('translate_amp'));
    }


    /**
     * @return boolean
     */
    public function getBrowserLanguageDetection() {
        return carbon_get_theme_option(self::getOptionName('browser_language_detection'));
    }


    /**
     * @return boolean
     */
    public function getCreateSwitchers() {
        return carbon_get_theme_option(self::getOptionName('create_switchers'));
    }


    /**
     * @return boolean
     */
    public function getRtl() {
        return carbon_get_theme_option(self::getOptionName('rtl'));
    }


    public function clear() {

        global $wpdb;

        delete_option(YAGLOT_SLUG);

        $options = implode(" OR ", array_map(function($option){
            return "option_name LIKE '" . self::getOptionName($option) . "%'";
        }, self::$options));

        $result = $wpdb->get_results( "
            SELECT 
                option_name
            FROM $wpdb->options
            WHERE " . $options . "
        ");

        foreach( $result as $option ) {
            delete_option( $option->option_name );
        }
    }
}


