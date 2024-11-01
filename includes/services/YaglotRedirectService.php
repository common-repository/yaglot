<?php

namespace Yaglot\Services;

use Yaglot\Helpers\UrlHelper;

if (!defined('ABSPATH')) {
    exit;
}

class YaglotRedirectService {

    /**
     * @param string $currentLanguage
     * @param string $cookieLanguage
     * @param array $languages
     * @param UrlHelper $yaglotUrl
     */
    public function autoRedirect($currentLanguage, $cookieLanguage, array $languages, UrlHelper $yaglotUrl) {

        if( ! empty($cookieLanguage)
            && in_array($cookieLanguage, $languages, true) ) {
            return;
        }

        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return;
        }

        $serverLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if ( in_array($serverLang, $languages, true)
            && $serverLang !== $currentLanguage ) {

            $url_auto_redirect = $yaglotUrl->getForLanguage($serverLang);

            wp_safe_redirect($url_auto_redirect);

            die();
        }
    }

}


