<?php

namespace Yaglot\Services;

use Yaglot\Client\Api\LanguageEntry;
use Yaglot\Helpers\UrlFilterHelper;
use Yaglot\Helpers\UrlHelper;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotSwitchersService {

    const MAIN_CLASS     = 'yg';
    const SWITCHER_CLASS = 'yg-sw';

    /**
     * @var YaglotOptionsService
     */
    private $optionsService;


    /**
     * @var string[]
     */
    private $targetLanguages;

    /**
     * @var string
     */
    private $originalLanguage;

    /**
     * @var string
     */
    private $currentLanguage;


    /**
     * @var LanguageEntry[]
     */
    private $languages;


    /**
     * @var \Yaglot\Helpers\UrlHelper
     */
    private $yaglotUrl;


    /**
     * YaglotSwitchersService constructor.
     * @param YaglotOptionsService $optionsService
     * @param YaglotLanguagesService $languagesService
     * @param UrlHelper $yaglotUrl
     * @param string $originalLanguage
     * @param string $currentLanguage
     * @param array $targetLanguages
     */
    public function __construct(YaglotOptionsService $optionsService,
                                YaglotLanguagesService $languagesService,
                                UrlHelper $yaglotUrl,
                                $originalLanguage,
                                $currentLanguage,
                                $targetLanguages = []) {

        $this->optionsService = $optionsService;
        $this->yaglotUrl = $yaglotUrl;
        $this->languages = $languagesService->getLanguagesConfigured();
        $this->originalLanguage = $originalLanguage;
        $this->targetLanguages = $targetLanguages;
        $this->currentLanguage = $currentLanguage;
    }


    /**
     * Get html switcher
     *
     * @return string
     * @param array $options
     */
    public function getHtml($options) {

        $id = $options['switcher_id'];
        $switcherData = $options['switcher_data'];
        $customClasses = $options['class'];
        $defaultStyles = $options['default_styles'];
        $offset = $options['offset'];
        $position = $options['fixed_position'] ? $options['positon'] : false;
        $flags = $options['flags'];
        $type = $options['type'];
        $shortTitle = $options['short_title'];

        $class = self::MAIN_CLASS;
        $class .= ' ' . self::SWITCHER_CLASS;

        $ampPage = false;
        $ampRegex = UrlFilterHelper::getAmpRegex(true);
        if ($this->optionsService->getTranslateAmp()
            && preg_match('/' . $ampRegex . '/', $this->yaglotUrl->getUrl())) {

            $class .= ' ' . self::MAIN_CLASS . '-invert';

            $ampPage = true;
        }

        $class .= ' ' . self::SWITCHER_CLASS . '-' . $type;
        $class .= ' ' . self::SWITCHER_CLASS . '-' . $id;

        if ($type !== 'select') {
            $class .= ' ' . self::SWITCHER_CLASS . '-flags-' . $flags;
        }

        if ($type === 'dropdown') {
            $class .= ' closed';
        }

        if ($switcherData === 'flags') {
            $class .= ' ' . self::SWITCHER_CLASS . '-hide-title';
        } else if ($switcherData === 'titles') {
            $class .= ' ' . self::SWITCHER_CLASS . '-hide-flag';
        }

        if (!$defaultStyles) {
            $class .= ' ' . self::SWITCHER_CLASS . '-no-styles';
        }

        $class .= ' ' . $customClasses;

        switch ($type) {

            case 'dropdown':

                $switcherHtml = $this->getDropdownSwitcherHtml($class, $shortTitle, $ampPage);

                break;

            case 'inline':

                $switcherHtml = $this->getInlineSwitcherHtml($class, $shortTitle);

                break;

            case 'select':

                $switcherHtml = $this->getSelectSwitcherHtml($class, $shortTitle, $ampPage);

                break;

            default:

                return '';

                break;
        }

        $containerClass = self::MAIN_CLASS;
        $containerClass .= ' ' . self::SWITCHER_CLASS . '-container';
        if ($position) {

            $containerClass .= ' ' . self::MAIN_CLASS . '-position-' . $position;

            $styles = $this->getContainerOffsets($position, $offset);
        } else {
            $styles = '';
        }

        $switcherHtml = '<div class="' . $containerClass . '" translate="no" ' . $styles . '>' . $switcherHtml;
        $switcherHtml .= '</div>';

        $switcherHtml .= sprintf('<!--YaGlot %s: Switcher-->', YAGLOT_VERSION);

        return $switcherHtml;
    }


    /**
     * @param string $position
     * @param string $offsets
     * @return string
     */
    private function getContainerOffsets($position, $offsets) {

        if (empty($offsets)) {
            return '';
        }

        switch ($position) {

            case 'top-left':

                $top = $offsets;
                $bottom = 'auto';
                $left = $offsets;
                $right = 'auto';

                break;

            case 'top-right':

                $top = $offsets;
                $bottom = 'auto';
                $left = 'auto';
                $right = $offsets;

                break;

            case 'bottom-left':

                $top = 'auto';
                $bottom = $offsets;
                $left = $offsets;
                $right = 'auto';

                break;

            case 'bottom-right':

                $top = 'auto';
                $bottom = $offsets;
                $left = 'auto';
                $right = $offsets;

                break;
        }

        $style = 'style="';
        $style .= 'top: ' . esc_attr($top) . ';';
        $style .= 'bottom: ' . esc_attr($bottom) . ';';
        $style .= 'left: ' . esc_attr($left) . ';';
        $style .= 'right: ' . esc_attr($right) . ';';
        $style .= '"';

        return $style;
    }

    /**
     * @param string $class
     * @param boolean $shortTitle
     * @return string
     */
    private function getInlineSwitcherHtml($class, $shortTitle) {

        $switcherHtml = '<aside class="' . esc_attr($class) . '">';

        $switcherHtml .= '<ul>';

        foreach (array_merge([$this->originalLanguage], $this->targetLanguages) as $code) {

            $title = (!$shortTitle)
                ? $this->languages[$code]->getEnglishName()
                : strtoupper($this->languages[$code]->getIso639());

            $active = $code === $this->currentLanguage ? ' active' : '';

            $switcherHtml .= '<li class="' . $code . $active . '" data-l="' . $code . '">';

            $switcherHtml .= '<a title="' . esc_attr($this->languages[$code]->getEnglishName()) . '" class="' . self::MAIN_CLASS . '" translate="no" href="' . esc_attr($this->yaglotUrl->getForLanguage($code)) . '">' . $title . '</a>';

            $switcherHtml .= '</li>';
        }

        $switcherHtml .= '</ul>';

        $switcherHtml .= '</aside>';

        return $switcherHtml;
    }


    /**
     * @param string $class
     * @param boolean $shortTitle
     * @param boolean $ampPage
     * @return string
     */
    private function getDropdownSwitcherHtml($class, $shortTitle, $ampPage = false) {

        $switcherHtml = '<aside class="' . esc_attr($class) . '">';

        $title = (!$shortTitle)
            ? $this->languages[$this->currentLanguage]->getEnglishName()
            : strtoupper($this->languages[$this->currentLanguage]->getIso639());


        $onClick = ($ampPage) ? 'onclick="' . esc_attr('YaglotSwitcher.onDropdownOpen(this.parentNode)') . '"' : '';

        $switcherHtml .= '<div class="active ' . $this->currentLanguage . '" data-l="' . $this->currentLanguage . '" ' . $onClick .'>';
        $switcherHtml .= '<a title="' . esc_attr($this->languages[$this->currentLanguage]->getEnglishName()) . '" class="' . self::MAIN_CLASS . '" translate="no" href="javascript:void(0);">' . $title . '</a>';
        $switcherHtml .= '</div>';

        $switcherHtml .= '<ul>';

        foreach (array_merge([$this->originalLanguage], $this->targetLanguages) as $code) {

            if ($code === $this->currentLanguage) {
                continue;
            }

            $title = (!$shortTitle)
                ? $this->languages[$code]->getEnglishName()
                : strtoupper($this->languages[$code]->getIso639());

            $switcherHtml .= '<li class="' . $code . '" data-l="' . $code . '">';

            $switcherHtml .= '<a title="' . esc_attr($this->languages[$code]->getEnglishName()) . '" class="' . self::MAIN_CLASS . '" translate="no" href="' . esc_attr($this->yaglotUrl->getForLanguage($code)) . '">' . $title . '</a>';

            $switcherHtml .= '</li>';
        }

        $switcherHtml .= '</ul>';
        $switcherHtml .= '</aside>';

        return $switcherHtml;
    }


    /**
     * @param string $class
     * @param boolean $shortTitle
     * @return string
     */
    private function getSelectSwitcherHtml($class, $shortTitle, $ampPage = false) {

        $onChange = ($ampPage) ? 'onchange="' . esc_attr('YaglotSwitcher.onSelectChange(this)') . '"' : '';

        $switcherHtml = '<select class="' . esc_attr($class) . '" ' . $onChange . '>';

        foreach (array_merge([$this->originalLanguage], $this->targetLanguages) as $code) {

            $title = (!$shortTitle)
                ? $this->languages[$code]->getEnglishName()
                : strtoupper($this->languages[$code]->getIso639());


            $selected = $code === $this->currentLanguage ? 'selected="true"' : '';
            $switcherHtml .= '<option class="' . self::MAIN_CLASS . '" value="' . $code . '" name="' . $code . '" data-href="' . esc_attr($this->yaglotUrl->getForLanguage($code)) . '" ' . $selected . '>' . $title . '</option>';
        }


        $switcherHtml .= '</select>';

        return $switcherHtml;
    }

}