<?php

namespace Yaglot\Services;

use Yaglot\Client\Api\Exception\ApiError;
use Yaglot\Client\Api\LanguageEntry;
use YaglotSimpleHtmlDom\simple_html_dom_node;

if (!defined('ABSPATH')) {
    exit;
}

class YaglotPageTranslationService {


    /**
     * @var YaglotParserService
     */
    private $parserService;


    /**
     * @var YaglotReplaceLinkService
     */
    private $replaceLinkService;


    /**
     * @var YaglotReplaceUrlService
     */
    private $replaceUrlService;


    /**
     * @var YaglotLanguagesService
     */
    private $languagesService;


    /**
     * @var YaglotRequestUrlService
     */
    private $requestUrlService;


    /**
     * @var YaglotOptionsService
     */
    private $optionsService;


    /**
     * @var YaglotSwitchersService
     */
    private $switchersService;


    /**
     * @var YaglotIntegrationsService
     */
    private $integrationsService;


    /**
     * @var string
     */
    private $originalLanguage;


    /**
     * @var string[]
     */
    private $targetLanguages = [];


    /**
     * @var string
     */
    private $currentLanguage;


    /**
     * @var array
     */
    private $switchers;


    /**
     * YaglotPageTranslationService constructor.
     * @param YaglotOptionsService $optionsService
     * @param YaglotRequestUrlService $requestUrlService
     * @param YaglotParserService $parserService
     * @param YaglotLanguagesService $languagesService
     * @param YaglotSwitchersService $switchersService
     * @param YaglotReplaceLinkService $replaceLinkService
     * @param YaglotReplaceUrlService $replaceUrlService
     * @param YaglotIntegrationsService $integrationsService
     * @param string $originalLanguage
     * @param string $currentLanguage
     * @param array $targetLanguages
     */
    public function __construct(YaglotOptionsService $optionsService,
                                YaglotRequestUrlService $requestUrlService,
                                YaglotParserService $parserService,
                                YaglotLanguagesService $languagesService,
                                YaglotSwitchersService $switchersService,
                                YaglotReplaceLinkService $replaceLinkService,
                                YaglotReplaceUrlService $replaceUrlService,
                                YaglotIntegrationsService $integrationsService,
                                $originalLanguage,
                                $currentLanguage,
                                $targetLanguages = []) {

        $this->currentLanguage = $currentLanguage;
        $this->targetLanguages = $targetLanguages;
        $this->originalLanguage = $originalLanguage;
        $this->parserService = $parserService;
        $this->integrationsService = $integrationsService;
        $this->replaceLinkService = $replaceLinkService;
        $this->replaceUrlService = $replaceUrlService;
        $this->optionsService = $optionsService;
        $this->requestUrlService = $requestUrlService;
        $this->languagesService = $languagesService;
        $this->switchersService = $switchersService;

        $this->switchers = $this->optionsService->getSwitchers();
    }


    /**
     * @param string $content
     * @return string
     */
    public function handlePageBuffer($content) {

        if ($this->requestUrlService->isSitemap($this->requestUrlService->getFullUrl())
            && $this->isXml($content)) {

            return $this->renderSitemap($content);
        }

        if ($this->isJson($content)) {
            $type = 'json';
        } else {
            $type = 'html';
        }

        if ($this->currentLanguage === $this->originalLanguage) {
            return $this->renderDom($content);
        }

        $parser = $this->parserService->getParser();

        try {

            switch ($type) {

                case 'json':

                    $json = json_decode($content, true);
                    $content = $this->translateArray($json);

                    return wp_json_encode($content);

                    break;

                case 'html':

                    $content = $this->fixMenuLink($content);
                    $translated_content = $parser->translate($content, $this->originalLanguage, $this->currentLanguage);

                    if ($this->integrationsService->isWoocommerceIntegrated()) {
                        $translated_content = $this->integrationsService->getWoocommerceTranslationService()->translateWords($translated_content);
                    }

                    return $this->renderDom($translated_content);

                    break;

            }

        } catch (ApiError $e) {

            $content .= '<!--YaGlot error API : ' . $this->removeComments($e->getMessage()) . '-->';

            return $this->renderDom($content);

        } catch (\Exception $e) {

            $content .= '<!--YaGlot error : ' . $this->removeComments($e->getMessage()) . '-->';
        }

        return $content;
    }


    /**
     * @param array $array
     * @return array
     */
    public function translateArray(array $array) {

        $array_not_ajax_html = ['redirecturl', 'url'];

        foreach ($array as $key => $val) {

            if (is_array($val)) {
                $array[$key] = $this->translateArray($val);
            } else {

                if ($this->isAjaxHtml($val)) {

                    $parser = $this->parserService->getParser();

                    try {
                        $array[$key] = $this->markToNoTranslate($parser->translate($val, $this->originalLanguage, $this->currentLanguage));
                    } catch (\Exception $e) {
                    }

                } elseif (in_array($key, $array_not_ajax_html)) {

                    $array[$key] = $this->replaceLinkService->replaceUrl($val, $this->currentLanguage);
                }
            }
        }

        return $array;
    }


    /**
     * @param string $string
     * @return bool
     */
    public function isAjaxHtml($string) {

        $preg_match_ajax_html = '/<(a|div|span|p|i|aside|input|textarea|select|h1|h2|h3|h4|meta|button|form|li|strong|ul|option)/';
        $result = preg_match_all($preg_match_ajax_html, $string, $m, PREG_PATTERN_ORDER);

        return isset($string[0]) && '{' !== $string[0] && $result && $result >= 1;
    }


    /**
     * @param string $string
     * @return bool
     */
    public function isJson($string) {

        return is_string($string)
            && is_array(json_decode($string, true))
            && JSON_ERROR_NONE === json_last_error();
    }


    /**
     * @param $string
     * @return bool
     */
    public function isXml($string) {

        if (!function_exists('libxml_use_internal_errors')) {
            return false;
        }

        if (!function_exists('simplexml_load_string')) {
            return false;
        }

        libxml_use_internal_errors(true);

        $doc = simplexml_load_string($string);

        if (!$doc) {

            libxml_clear_errors();

            return false;
        }

        return true;
    }

    /**
     * @param string $html
     * @return string
     */
    private function removeComments($html) {
        return preg_replace('/<!--(.*)-->/Uis', '', $html);
    }


    /**
     * @param string $dom
     * @return string
     */
    public function replaceLinks($dom) {

        $dom = $this->replaceUrlService->modifyLink('/<a([^\>]+?)?href=(\"|\')([^\s\>]+?)(\"|\')([^\>]+?)?>/', $dom, 'A');
        $dom = $this->replaceUrlService->modifyLink('/<([^\>]+?)?data-link=(\"|\')([^\s\>]+?)(\"|\')([^\>]+?)?>/', $dom, 'DataLink');
        $dom = $this->replaceUrlService->modifyLink('/<([^\>]+?)?data-url=(\"|\')([^\s\>]+?)(\"|\')([^\>]+?)?>/', $dom, 'DataUrl');
        $dom = $this->replaceUrlService->modifyLink('/<([^\>]+?)?data-cart-url=(\"|\')([^\s\>]+?)(\"|\')([^\>]+?)?>/', $dom, 'DataCart');
        $dom = $this->replaceUrlService->modifyLink('/<form([^\>]+?)?action=(\"|\')([^\s\>]+?)(\"|\')/', $dom, 'Form');
        $dom = $this->replaceUrlService->modifyLink('/<link rel="canonical"(.*?)?href=(\"|\')([^\s\>]+?)(\"|\')/', $dom, 'Canonical');
        $dom = $this->replaceUrlService->modifyLink('/<link rel="amphtml"(.*?)?href=(\"|\')([^\s\>]+?)(\"|\')/', $dom, 'Amp');
        $dom = $this->replaceUrlService->modifyLink('/<meta property="og:url"(.*?)?content=(\"|\')([^\s\>]+?)(\"|\')/', $dom, 'Meta');

        return $dom;
    }


    /**
     * @param string $content
     * @return string
     */
    public function fixMenuLink($content) {

        $content = preg_replace('#<a([^\>]+?)?href="(http|https):\/\/\[yaglot_#', '<a$1 translate="no" href="$2://[yaglot_', $content);

        return $content;
    }


    /**
     * @param string $dom
     * @return string
     */
    public function addSwitcher($dom) {

        if (!$this->optionsService->getCreateSwitchers()) {
            return $dom;
        }

        foreach ($this->switchers as $switcher) {

            if (!$switcher['fixed_position']) {
                continue;
            }

            $switcherHtml = $this->switchersService->getHtml($switcher);

            $dom = str_replace('</body>', $switcherHtml . ' </body>', $dom);
        }

        return $dom;
    }


    /**
     * @param string $dom
     * @return string
     */
    public function addCustomCss($dom) {

        if (!$this->optionsService->getCreateSwitchers()) {
            return $dom;
        }

        foreach ($this->switchers as $switcher) {

            if (empty($switcher['css'])) {
                continue;
            }

            $class = '.' . YaglotSwitchersService::MAIN_CLASS
                . '.' . YaglotSwitchersService::SWITCHER_CLASS
                . '.' . YaglotSwitchersService::SWITCHER_CLASS . '-' . $switcher['switcher_id'];

            $css = preg_replace("/[{]{2}[\s]*id[\s]*[}]{2}/", $class, $switcher['css']);
            $styles = "<style type='text/css'>{$css}</style>";
            $dom = str_replace("</head>", "{$styles}</head>", $dom);
        }

        return $dom;
    }


    /**
     * @param string $dom
     * @return string
     */
    public function parseMenu($dom) {

        if (strpos($dom, '[yaglot_menu') !== false) {

            /**
             * @var LanguageEntry[] $languages_configured
             */
            $languages_configured = $this->languagesService->getLanguagesConfigured();

            foreach ($languages_configured as $language) {

                $shortcode_title = sprintf('\[yaglot_menu_title-%s\]', $language->getIso639());
                $shortcode_title_without_bracket = sprintf('yaglot_menu_title-%s', $language->getIso639());
                $shortcode_title_html = str_replace('\[', '%5B', $shortcode_title);
                $shortcode_title_html = str_replace('\]', '%5D', $shortcode_title_html);
                $shortcode_url = sprintf('(http|https):\/\/\[yaglot_menu_current_url-%s\]', $language->getIso639());
                $shortcode_url_html = str_replace('\[', '%5B', $shortcode_url);
                $shortcode_url_html = str_replace('\]', '%5D', $shortcode_url_html);

                $url = $this->requestUrlService->getYaglotUrl();

                $dom = preg_replace('/' . $shortcode_title . '/i', $language->getEnglishName(), $dom);
                $dom = preg_replace('/' . $shortcode_title_html . '/i', $language->getEnglishName(), $dom);
                $dom = preg_replace('/' . $shortcode_title_without_bracket . '/i', $language->getEnglishName(), $dom);

                $link_menu = $url->getForLanguage($language->getIso639());

                $dom = preg_replace('/' . $shortcode_url . '/i', $link_menu, $dom);
                $dom = preg_replace('/' . $shortcode_url_html . '/i', $link_menu, $dom);
            }
        }

        return $dom;
    }


    /**
     * @param string $dom
     * @return string
     */
    public function renderDom($dom) {

        $dom = $this->parseMenu($dom);
        $dom = $this->addSwitcher($dom);
        $dom = $this->addCustomCss($dom);


        // We only need this on translated page
        if ($this->currentLanguage !== $this->originalLanguage) {

            $dom = $this->replaceLinks($dom);

            $dom = preg_replace('/<html (.*?)?lang=(\"|\')(\S*)(\"|\')/', '<html $1lang=$2' . $this->currentLanguage . '$4', $dom);
            $dom = preg_replace('/property="og:locale" content=(\"|\')(\S*)(\"|\')/', 'property="og:locale" content=$1' . $this->currentLanguage . '$3', $dom);
        }

        return $dom;
    }


    /**
     * @param string $content
     * @return string
     */
    private function renderSitemap($content) {

        if(!class_exists('DOMDocument')) {
            return $content;
        }

        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($content);

        foreach($dom->getElementsByTagName('loc') as $loc) {

            $host = parse_url($loc->textContent, PHP_URL_HOST);
            if( empty($host) ) {
                continue;
            }

            $url = $loc->nodeValue;

            foreach ($this->targetLanguages as $language) {

                $urlObject = $this->requestUrlService->createUrlObject($url);

                $node = $dom->createElement('xhtml:link');
                $node->setAttribute('rel', 'alternate');
                $node->setAttribute('hreflang', $language);
                $node->setAttribute('href', $urlObject->getForLanguage($language));

                if ($loc->nextSibling) {
                    $loc->parentNode->insertBefore($node, $loc->nextSibling);
                } else {
                    $loc->parentNode->appendChild($node);
                }
            }
        }

        $dom->formatOutput = true;

        return $dom->saveXML();
    }


    /**
     * @param $content
     * @return string
     */
    public function markToNoTranslate($content) {

        $dom = \YaglotSimpleHtmlDom\str_get_html(
            $content,
            true,
            true,
            DEFAULT_TARGET_CHARSET,
            false
        );

        if ($dom === false) {
            return $content;
        }

        foreach ($dom->nodes as $node) {
            /**
             * @var simple_html_dom_node $node
             */
            $node->setAttribute('translate', 'no');
        }

        return $dom->save();
    }

}