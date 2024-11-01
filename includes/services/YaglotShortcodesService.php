<?php

namespace Yaglot\Services;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotShortcodesService {


    /**
     * @var YaglotSwitchersService
     */
    private $switchersService;


    /**
     * @var YaglotRequestUrlService
     */
    private $requestUrlService;


    /**
     * @var YaglotOptionsService
     */
    private $optionsService;


    /**
     * @var array
     */
    private $switchers = [];


    /**
     * @var bool
     */
    private $createSwitchers;


    /**
     * YaglotShortcodesService constructor.
     * @param YaglotSwitchersService $switchersService
     * @param YaglotRequestUrlService $requestUrlService
     * @param YaglotOptionsService $optionsService
     */
    public function __construct(YaglotSwitchersService $switchersService,
                                YaglotRequestUrlService $requestUrlService,
                                YaglotOptionsService $optionsService) {

        $this->switchersService = $switchersService;
        $this->requestUrlService = $requestUrlService;
        $this->optionsService = $optionsService;

        foreach ($this->optionsService->getSwitchers() as $switcher) {
            $this->switchers[$switcher['switcher_id']] = $switcher;
        }

        $this->createSwitchers = $this->optionsService->getCreateSwitchers();

        add_shortcode('yaglot_switcher', [$this, 'handleSwitcherShortcode']);
    }


    /**
     * @param array
     * @return string
     */
    public function handleSwitcherShortcode($atts) {

        if (!isset($atts['id'])) {
            return '';
        }

        if (!isset($this->switchers[$atts['id']])) {
            return '';
        }

        if (!$this->requestUrlService->isTranslatableUrl()) {
            return '';
        }

        if(!$this->createSwitchers) {
            return '';
        }

        $options = $this->switchers[$atts['id']];

        $options['fixed_position'] = false;

        return $this->switchersService->getHtml($options);
    }
}