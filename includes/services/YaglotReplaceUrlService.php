<?php

namespace Yaglot\Services;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotReplaceUrlService {

    /**
     * @var YaglotRequestUrlService
     */
    private $requestUrlService;


    /**
     * @var YaglotReplaceLinkService
     */
    private $replaceLinkService;


    /**
     * YaglotReplaceUrlService constructor.
     * @param YaglotRequestUrlService $requestUrlService
     * @param YaglotReplaceLinkService $replaceLinkService
     */
    public function __construct(YaglotRequestUrlService $requestUrlService,
                                YaglotReplaceLinkService $replaceLinkService) {

        $this->requestUrlService = $requestUrlService;
        $this->replaceLinkService = $replaceLinkService;
    }


    /**
     * Replace link
     *
     * @param string $pattern
     * @param string $translated_page
     * @param string $type
     * @return string
     */
    public function modifyLink($pattern, $translated_page, $type) {

        preg_match_all($pattern, $translated_page, $out, PREG_PATTERN_ORDER);

        $count_out_0 = count($out[0]);
        for ($i = 0; $i < $count_out_0; $i++) {

            $sometags = (isset($out[1])) ? $out[1][$i] : null;
            $quote1 = (isset($out[2])) ? $out[2][$i] : null;
            $current_url = (isset($out[3])) ? $out[3][$i] : null;
            $quote2 = (isset($out[4])) ? $out[4][$i] : null;
            $sometags2 = (isset($out[5])) ? $out[5][$i] : null;

            $length_link = 1500; // Prevent error on long URL (preg_match_all Compilation failed: regular expression is too large at offset)
            if (strlen($current_url) >= $length_link) {
                continue;
            }

            if (self::checkLink($current_url, $sometags, $sometags2)) {

                $function_name = 'replace' . $type;

                if( $function_name === 'replaceA' ) {
                    $translated_page = $this->replaceLinkService->$function_name(
                        $translated_page,
                        $current_url,
                        $quote1,
                        $quote2,
                        $sometags,
                        $sometags2
                    );
                } else {
                    $translated_page = $this->replaceLinkService->$function_name(
                        $translated_page,
                        $current_url,
                        $quote1,
                        $quote2,
                        $sometags
                    );
                }
            }
        }

        return $translated_page;
    }


    /**
     * @param string $current_url
     * @param string $sometags
     * @param string $sometags2
     * @return string
     */
    public function checkLink($current_url, $sometags = null, $sometags2 = null) {

        $admin_url = admin_url();
        $parsed_url = wp_parse_url($current_url);

        return (
            (
                ($current_url[0] === 'h' && $parsed_url['host'] === $_SERVER['HTTP_HOST']) || //phpcs:ignore
                (isset($current_url[0]) && $current_url[0] === '/' && (isset($current_url[1])) && '/' !== $current_url[1]) //phpcs:ignore
            ) &&
            strpos($current_url, $admin_url) === false
            && strpos($current_url, 'wp-login') === false
            && !$this->isLinkAFile($current_url)
            && $this->requestUrlService->isEligibleUrl($current_url)
            && strpos($sometags, 'translate') === false
            && strpos($sometags2, 'translate') === false
        );
    }


    /**
     * @param string $current_url
     * @return boolean
     */
    public function isLinkAFile($current_url) {

        $files = [
            'pdf',
            'rar',
            'doc',
            'docx',
            'jpg',
            'jpeg',
            'png',
            'ppt',
            'pptx',
            'xls',
            'zip',
            'mp4',
            'xlsx',
            'gif',
            'wav',
            'mp3',
            'txt',
            'numbers'
        ];

        foreach ($files as $file) {
            if (self::endsWith($current_url, '.' . $file)) {
                return true;
            }
        }

        return false;
    }


    /**
     * search forward starting from end minus needle length characters
     * @param string $haystack
     * @param string $needle
     * @return boolean
     */
    public function endsWith($haystack, $needle) {

        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

}