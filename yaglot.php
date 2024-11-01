<?php

/*
Plugin Name: YaGlot
Plugin URI: https://yaglot.com/
Description: Need translate a website? YaGlot plugin will make your website multilingual easily. Go global and build international business with us!
Author: DevIT Team
Author URI: https://devit-team.com/
Text Domain: yaglot
Domain Path: /languages/
Version: 1.0.5
*/

if (!defined('ABSPATH')) {
    exit;
}

define('YAGLOT_VERSION', '1.0.5');
define('YAGLOT_EMAIL', 'support@yaglot.com');
define('YAGLOT_NAME', 'YaGlot');
define('YAGLOT_SLUG', 'yaglot');
define('YAGLOT_PREFIX', 'yg_');
define('YAGLOT_PHP_MIN', '5.6');
define('YAGLOT_DASHBOARD_URL', 'https://dashboard.yaglot.com/');
define('YAGLOT_API_URL', 'https://api.yaglot.com/');
define('YAGLOT_SITE_URL', 'https://yaglot.com/');
define('YAGLOT_CDN_URL', 'https://cdn.yaglot.com/');
define('YAGLOT_DOCUMENTATION_URL', 'https://help.yaglot.com/');
define('YAGLOT_BILLING_URL', YAGLOT_DASHBOARD_URL . '%s/billing');
define('YAGLOT_TRANSLATIONS_URL', YAGLOT_DASHBOARD_URL . '%s/project/%s/translation-list');
define('YAGLOT_FILE', __FILE__);
define('YAGLOT_DIR', __DIR__);
define('YAGLOT_URL', plugin_dir_url(__FILE__));
define('YAGLOT_URL_IMAGES', YAGLOT_URL . 'assets/images/');
define('YAGLOT_SUPPORT_FORM_SUBJECT', 'Yaglot WordPress Support');

/**
 * @param array $parts
 * @return string
 */
function yaglot_path(array $parts) {
    return YAGLOT_DIR . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);
}

function yaglot_plugin_loaded() {

    load_plugin_textdomain( YAGLOT_SLUG, false, dirname(plugin_basename(YAGLOT_FILE)) . '/languages/' );
    load_plugin_textdomain( 'carbon-fields', false, dirname(plugin_basename(YAGLOT_FILE)) . '/vendor/htmlburger/carbon-fields/languages/' );

    if (version_compare(PHP_VERSION, YAGLOT_PHP_MIN) < 0) {

        add_action('admin_notices', 'yaglot_php_min_compatibility');

        return;
    }

    if (function_exists('apache_get_modules') && !in_array('mod_rewrite', apache_get_modules())) {

        add_action('admin_notices', 'yaglot_rewrite_module_disabled');

        return;
    }

    if (!function_exists('curl_version')) {

        add_action('admin_notices', 'yaglot_curl_missing');

        return;
    }

    require_once yaglot_path(['includes.php']);

    \Yaglot\YaglotTranslate::getInstance();
}

function yaglot_plugin_activate() {

    require_once yaglot_path(['includes.php']);

    \Yaglot\YaglotTranslate::getInstance()->activate();
}

function yaglot_plugin_deactivate() {

    require_once yaglot_path(['includes.php']);

    \Yaglot\YaglotTranslate::getInstance()->deactivate();
}

function yaglot_plugin_uninstall() {

    require_once yaglot_path(['includes.php']);

    \Yaglot\YaglotTranslate::getInstance()->uninstall();
}


function yaglot_php_min_compatibility() {
    include yaglot_path(['notices', 'php-min.php']);
}

function yaglot_rewrite_module_disabled() {
    include yaglot_path(['notices', 'rewrite-module.php']);
}

function yaglot_curl_missing() {
    include yaglot_path(['notices', 'no-curl.php']);
}


add_action('plugins_loaded', 'yaglot_plugin_loaded');

register_activation_hook(YAGLOT_FILE, 'yaglot_plugin_activate');
register_deactivation_hook(YAGLOT_FILE, 'yaglot_plugin_deactivate');
register_uninstall_hook(YAGLOT_FILE, 'yaglot_plugin_uninstall');