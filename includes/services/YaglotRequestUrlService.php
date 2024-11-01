<?php

namespace Yaglot\Services;

use Yaglot\Helpers\ServerHelper;
use Yaglot\Helpers\UrlFilterHelper;
use Yaglot\Helpers\UrlHelper;

if (!defined('ABSPATH')) {
    exit;
}

class YaglotRequestUrlService {


    /**
     * @var UrlHelper
     */
    private $yaglotUrl = null;


    /**
     * @var array
     */
    private $excludedUrls = [];


    /**
     * @var string
     */
    private $originalLanguage;


    /**
     * @var string[]
     */
    private $targetLanguages;


    /**
     * @var YaglotOptionsService
     */
    private $optionsService;


    /**
     * YaglotRequestUrlService constructor.
     * @param YaglotOptionsService $optionsService
     * @param string $originalLanguage
     * @param array $targetLanguages
     * @param array $excludedUrls
     */
    public function __construct(YaglotOptionsService $optionsService,
                                $originalLanguage,
                                $targetLanguages = [],
                                $excludedUrls = []) {

        $this->optionsService = $optionsService;
        $this->originalLanguage = $originalLanguage;
        $this->targetLanguages = $targetLanguages;
        $this->excludedUrls = $excludedUrls;
    }

    /**
     * @param bool $useForwardedHost
     * @return string
     */
    public function getFullUrl($useForwardedHost = false) {
        return ServerHelper::fullUrl($_SERVER, $useForwardedHost);
    }


    public function getCurrentLanguage() {

        if (wp_doing_ajax() && isset($_SERVER['HTTP_REFERER'])) {
            return $this->createUrlObject($_SERVER['HTTP_REFERER'])->detectCurrentLanguage();
        }

        return $this->getYaglotUrl()->detectCurrentLanguage();
    }


    public function getYaglotUrl() {

        if (null === $this->yaglotUrl) {
            $this->initYaglotUrl();
        }

        return $this->yaglotUrl;
    }


    public function initYaglotUrl() {

        $exclude_urls_option = $this->excludedUrls;

        if (!empty($exclude_urls_option)) {
            $exclude_urls_option = array_map(function ($item) {
                return $this->urlToRelative($item);
            }, $exclude_urls_option);
        }

        $this->yaglotUrl = new UrlHelper(
            $this->getFullUrl(),
            $this->originalLanguage,
            $this->targetLanguages,
            $this->getHomeWordpressDirectory()
        );

        $this->yaglotUrl->setExcludedUrls($exclude_urls_option);

        return $this;
    }


    /**
     * @param string $url
     * @return UrlHelper
     */
    public function createUrlObject($url) {
        return new UrlHelper(
            $url,
            $this->originalLanguage,
            $this->targetLanguages,
            $this->getHomeWordpressDirectory()
        );
    }


    /**
     * @param string $url
     * @return false|int
     */
    public function isSitemap($url) {
        return preg_match("/sitemap.xml$/", $url);
    }


    /**
     * @param string $url
     * @return bool
     */
    public function isEligibleUrl($url) {

        $url = urldecode($this->urlToRelative($url));

        $exclude_urls_option = $this->excludedUrls;

        if (!empty($exclude_urls_option)) {

            $exclude_urls_option = implode(',', $exclude_urls_option);
            $exclude_urls_option = preg_replace('#\s+#', ',', trim($exclude_urls_option));

            $excluded_urls = explode(',', $exclude_urls_option);
            foreach ($excluded_urls as $key => $ex_url) {
                $excluded_urls[$key] = $this->urlToRelative($ex_url); //phpcs:ignore
            }

            $exclude_urls_option = implode(',', $excluded_urls);
        }

        $exclusions = preg_replace('#\s+#', ',', $exclude_urls_option);

        $list_regex = [];
        if (!empty($exclusions)) {
            $list_regex = explode(',', $exclusions);
        }

        if (!$this->optionsService->getTranslateAmp()) {
            $list_regex[] = UrlFilterHelper::getAmpRegex();
        }

        foreach ($list_regex as $regex) {
            $str = $this->escapeSlash($regex);
            $prepare_regex = sprintf('/%s/', $str);
            if (preg_match($prepare_regex, $url) === 1) {
                return false;
            }
        }

        return true;
    }


    /**
     * @return bool
     */
    public function isTranslatableUrl() {
        return $this->getYaglotUrl()->isTranslable();
    }


    /**
     * @param string $url
     * @return string
     */
    public function urlToRelative($url) {

        if ((substr($url, 0, 7) === 'http://') || (substr($url, 0, 8) === 'https://')) {

            // the current link is an "absolute" URL - parse it to get just the path
            $parsed = wp_parse_url($url);
            $path = isset($parsed['path']) ? $parsed['path'] : '';
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

            if ($this->getHomeWordpressDirectory()) {

                $relative = str_replace($this->getHomeWordpressDirectory(), '', $path);

                return (empty($relative)) ? '/' : $relative;
            }

            return $path . $query . $fragment;
        }

        return $url;
    }


    /**
     * @return null|string
     */
    public function getHomeWordpressDirectory() {

        $opt_siteurl = trim(get_option('siteurl'), '/');
        $opt_home = trim(get_option('home'), '/');

        if (empty($opt_siteurl) || empty($opt_home)) {
            return null;
        }

        if ((substr($opt_home, 0, 7) === 'http://' && strpos(substr($opt_home, 7), '/') !== false)
            || (substr($opt_home, 0, 8) === 'https://' && strpos(substr($opt_home, 8), '/') !== false)) {
            $parsed_url = parse_url($opt_home);
            $path = isset($parsed_url['path']) ? $parsed_url['path'] : '/';

            return $path;
        }

        return null;
    }


    /**
     * @param string $str
     * @return string
     */
    public function escapeSlash($str) {
        return str_replace('/', '\/', $str);
    }
}


