<?php

namespace Yaglot\Helpers;

use Yaglot\Services\YaglotRequestUrlService;

if (!defined('ABSPATH')) {
    exit;
}

class UrlFilterHelper {


    /**
     * @param bool $withEscape
     * @return string
     */
    public static function getAmpRegex( $withEscape = false ) {

        $regex = '([&\?/])amp(/)?$';
        if ( $withEscape ) {
            $regex = str_replace( '/', '\/', $regex );
        }

        return $regex;
    }


    /**
     * @param string $url
     * @return string
     */
    protected static function getCleanBaseUrl($url) {

        if (strpos($url, 'http') === false) {
            $url = sprintf('%s%s', get_site_url(), $url);
        }

        return $url;
    }


    /**
     * Filter URL log redirection
     *
     * @param string $urlFilter
     * @param string $currentLanguage
     * @param string $originalLanguage
     * @param YaglotRequestUrlService $requestUrlService
     * @return string
     */
    public static function filterUrlLogRedirect($urlFilter, $currentLanguage, $originalLanguage, YaglotRequestUrlService $requestUrlService) {

        $choose_current_language = $currentLanguage;

        $urlFilter = self::getCleanBaseUrl($urlFilter);

        $url = $requestUrlService->createUrlObject($urlFilter);

        if ($currentLanguage === $originalLanguage
            && isset($_SERVER['HTTP_REFERER'])
        ) {
            $url = $requestUrlService->createUrlObject($_SERVER['HTTP_REFERER']);
            $choose_current_language = $url->detectCurrentLanguage();

            if ($choose_current_language !== $originalLanguage) {
                $url = $requestUrlService->createUrlObject($urlFilter);
            }
        }

        return $url->getForLanguage($choose_current_language);
    }


    /**
     * Filter url without Ajax
     *
     * @param string $urlFilter
     * @param string $currentLanguage
     * @param string $originalLanguage
     * @param YaglotRequestUrlService $requestUrlService
     * @return string
     */
    public static function filterUrlWithoutAjax($urlFilter, $currentLanguage, $originalLanguage, YaglotRequestUrlService $requestUrlService) {

        if ($currentLanguage === $originalLanguage) {
            return $urlFilter;
        }

        $url = $requestUrlService->createUrlObject($urlFilter);

        return $url->getForLanguage($currentLanguage);
    }


    /**
     * Filter url with optional Ajax
     * @param string $urlFilter
     * @param string $currentLanguage
     * @param string $originalLanguage
     * @param YaglotRequestUrlService $requestUrlService
     * @return string
     */
    public static function filterUrlWithAjax($urlFilter, $currentLanguage, $originalLanguage, YaglotRequestUrlService $requestUrlService) {

        $choose_current_language = $currentLanguage;
        if ($currentLanguage !== $originalLanguage) {
            $url = $requestUrlService->createUrlObject($urlFilter);
        } else {
            if (isset($_SERVER['HTTP_REFERER'])) {
                $url = $requestUrlService->createUrlObject($_SERVER['HTTP_REFERER']);
                $choose_current_language = $url->detectCurrentLanguage();
                $url = $requestUrlService->createUrlObject($urlFilter);
            }
        }

        return $url->getForLanguage($choose_current_language);
    }

}
