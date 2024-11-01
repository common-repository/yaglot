<?php

namespace Yaglot\Services;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotReplaceLinkService {


    /**
     * @var YaglotMultisiteService
     */
    private $multisiteService;


    /**
     * @var string 
     */
    private $originalLanguage;


    /**
     * @var string 
     */
    private $currentLanguage;


    /**
     * YaglotReplaceLinkService constructor.
     * @param YaglotMultisiteService $multisiteService
     * @param string $originalLanguage
     * @param string $currentLanguage
     */
    public function __construct(YaglotMultisiteService $multisiteService, $originalLanguage, $currentLanguage) {
        $this->multisiteService = $multisiteService;
        $this->originalLanguage = $originalLanguage;
        $this->currentLanguage = $currentLanguage;
    }

    /**
     * Replace an URL
     * @param string $currentUrl
     * @param string $currentLanguage
     * @return string
     */
    public function replaceUrl($currentUrl, $currentLanguage) {

        $originalLanguage = $this->originalLanguage;

        $parsed_url = wp_parse_url($currentUrl);
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '/';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';


        if ($currentLanguage === $originalLanguage) {
            return $currentUrl;
        } else {
            $url_translated = (strlen($path) > 2 && substr($path, 0, 4) === "/$currentLanguage/") ?
                "$scheme$user$pass$host$port$path$query$fragment" : "$scheme$user$pass$host$port/$currentLanguage$path$query$fragment";

            foreach (array_reverse($this->multisiteService->getListOfNetworkPath()) as $np) {
                if (strlen($np) > 2 && strpos($url_translated, $np) !== false) {
                    $url_translated = str_replace(
                        str_replace('//', '/', '/' . $currentLanguage . $np . '/'),
                        str_replace('//', '/', $np . '/' . $currentLanguage . '/'),
                        $url_translated
                    );
                }
            }

            return $url_translated;
        }
    }


    /**
     * Replace href in <a>
     * @param string $translated_page
     * @param string $current_url
     * @param string $quote1
     * @param string $quote2
     * @param string $sometags
     * @param string $sometags2
     * @return string
     */
    public function replaceA($translated_page, $current_url, $quote1, $quote2, $sometags = null, $sometags2 = null) {

        $current_language = $this->currentLanguage;

        $translated_page = preg_replace('/<a' . preg_quote($sometags, '/') . 'href=' . preg_quote($quote1 . $current_url . $quote2, '/') . preg_quote($sometags2, '/') . '>/', '<a' . $sometags . 'href=' . $quote1 . $this->replaceUrl($current_url, $current_language) . $quote2 . $sometags2 . '>', $translated_page);

        return $translated_page;
    }


    /**
     * Replace data-link attribute
     *
     * @param string $translated_page
     * @param string $current_url
     * @param string $quote1
     * @param string $quote2
     * @param string $sometags
     * @return string
     */
    public function replaceDataLink($translated_page, $current_url, $quote1, $quote2, $sometags = null) {

        $current_language = $this->currentLanguage;

        $translated_page = preg_replace('/<' . preg_quote($sometags, '/') . 'data-link=' . preg_quote($quote1 . $current_url . $quote2, '/') . '/', '<' . $sometags . 'data-link=' . $quote1 . $this->replaceUrl($current_url, $current_language) . $quote2, $translated_page);

        return $translated_page;
    }


    /**
     * Replace data-url attribute
     *
     * @param string $translated_page
     * @param string $current_url
     * @param string $quote1
     * @param string $quote2
     * @param string $sometags
     * @return string
     */
    public function replaceDataUrl($translated_page, $current_url, $quote1, $quote2, $sometags = null) {

        $current_language = $this->currentLanguage;

        $translated_page = preg_replace('/<' . preg_quote($sometags, '/') . 'data-url=' . preg_quote($quote1 . $current_url . $quote2, '/') . '/', '<' . $sometags . 'data-url=' . $quote1 . $this->replaceUrl($current_url, $current_language) . $quote2, $translated_page);

        return $translated_page;
    }


    /**
     * Replace data-cart-url attribute
     *
     * @param string $translated_page
     * @param string $current_url
     * @param string $quote1
     * @param string $quote2
     * @param string $sometags
     * @return string
     */
    public function replaceDatacart($translated_page, $current_url, $quote1, $quote2, $sometags = null) {

        $current_language = $this->currentLanguage;

        $translated_page = preg_replace('/<' . preg_quote($sometags, '/') . 'data-cart-url=' . preg_quote($quote1 . $current_url . $quote2, '/') . '/', '<' . $sometags . 'data-cart-url=' . $quote1 . $this->replaceUrl($current_url, $current_language) . $quote2, $translated_page);

        return $translated_page;
    }


    /**
     * Replace form action attribute
     *
     * @param string $translated_page
     * @param string $current_url
     * @param string $quote1
     * @param string $quote2
     * @param string $sometags
     * @return string
     */
    public function replaceForm($translated_page, $current_url, $quote1, $quote2, $sometags = null) {

        $current_language = $this->currentLanguage;

        $translated_page = preg_replace('/<form' . preg_quote($sometags, '/') . 'action=' . preg_quote($quote1 . $current_url . $quote2, '/') . '/', '<form ' . $sometags . 'action=' . $quote1 . $this->replaceUrl($current_url, $current_language) . $quote2, $translated_page);

        return $translated_page;
    }


    /**
     * Replace canonical attribute
     *
     * @param string $translated_page
     * @param string $current_url
     * @param string $quote1
     * @param string $quote2
     * @param string $sometags
     * @return string
     */
    public function replaceCanonical($translated_page, $current_url, $quote1, $quote2, $sometags = null) {

        $current_language = $this->currentLanguage;

        $translated_page = preg_replace('/<link rel="canonical"' . preg_quote($sometags, '/') . 'href=' . preg_quote($quote1 . $current_url . $quote2, '/') . '/', '<link rel="canonical"' . $sometags . 'href=' . $quote1 . $this->replaceUrl($current_url, $current_language) . $quote2, $translated_page);

        return $translated_page;
    }


    /**
     * Replace amphtml attribute
     *
     * @param string $translated_page
     * @param string $current_url
     * @param string $quote1
     * @param string $quote2
     * @param string $sometags
     * @return string
     */
    public function replaceAmp($translated_page, $current_url, $quote1, $quote2, $sometags = null) {

        $current_language = $this->currentLanguage;

        $translated_page = preg_replace('/<link rel="amphtml"' . preg_quote($sometags, '/') . 'href=' . preg_quote($quote1 . $current_url . $quote2, '/') . '/', '<link rel="amphtml"' . $sometags . 'href=' . $quote1 . $this->replaceUrl($current_url, $current_language) . $quote2, $translated_page);

        return $translated_page;
    }


    /**
     * Replace meta og url attribute
     *
     * @param string $translated_page
     * @param string $current_url
     * @param string $quote1
     * @param string $quote2
     * @param string $sometags
     * @return string
     */
    public function replaceMeta($translated_page, $current_url, $quote1, $quote2, $sometags = null) {

        $current_language = $this->currentLanguage;

        $translated_page = preg_replace('/<meta property="og:url"' . preg_quote($sometags, '/') . 'content=' . preg_quote($quote1 . $current_url . $quote2, '/') . '/', '<meta property="og:url"' . $sometags . 'content=' . $quote1 . $this->replaceUrl($current_url, $current_language) . $quote2, $translated_page);

        return $translated_page;
    }


}