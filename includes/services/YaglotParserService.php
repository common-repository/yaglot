<?php

namespace Yaglot\Services;

use Yaglot\Client\Client;
use Yaglot\Parser\ConfigProvider\ManualConfigProvider;
use Yaglot\Parser\Parser;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotParserService {


    /**
     * @var YaglotOptionsService
     */
    private $optionsService;


    /**
     * @var YaglotRequestUrlService
     */
    private $requestUrlService;


    /**
     * YaglotParserService constructor.
     * @param YaglotOptionsService $optionsService
     * @param YaglotRequestUrlService $requestUrlService
     */
    public function __construct(YaglotOptionsService $optionsService,
                                YaglotRequestUrlService $requestUrlService) {

        $this->optionsService = $optionsService;
        $this->requestUrlService = $requestUrlService;
    }

    /**
     * @return Parser
     */
    public function getParser() {

        $config = new ManualConfigProvider($this->requestUrlService->getFullUrl());

        $client = new Client($this->optionsService->getApiKey());

        $client->getProfile()->setIgnoredNodes(false);

        $parser = new Parser($client, $config, $this->optionsService->getExcludedBlocks());

        return $parser;
    }

}