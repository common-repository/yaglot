<?php

namespace Yaglot\Parser;

use YaglotSimpleHtmlDom\simple_html_dom;
use Yaglot\Client\Api\Exception\ApiError;
use Yaglot\Client\Api\Exception\InputAndOutputCountMatchException;
use Yaglot\Client\Api\Exception\InvalidWordTypeException;
use Yaglot\Client\Api\Exception\MissingRequiredParamException;
use Yaglot\Client\Api\Exception\MissingWordsOutputException;
use Yaglot\Client\Api\TranslateEntry;
use Yaglot\Client\Api\WordCollection;
use Yaglot\Client\Client;
use Yaglot\Client\Endpoint\Translate;
use Yaglot\Parser\Check\DomCheckerProvider;
use Yaglot\Parser\Check\JsonLdChecker;
use Yaglot\Parser\ConfigProvider\ConfigProviderInterface;
use Yaglot\Parser\Formatter\DomFormatter;
use Yaglot\Parser\Formatter\ExcludeBlocksFormatter;
use Yaglot\Parser\Formatter\IgnoredNodes;
use Yaglot\Parser\Formatter\JsonLdFormatter;
use Yaglot\Util\DomAppend;
use Yaglot\Util\Element\DomAppendElement;

/**
 * Class Parser
 * @package Yaglot\Parser
 */
class Parser
{
    /**
     * Attribute to match in DOM when we don't want to translate innertext & childs.
     */
    const ATTRIBUTE_NO_TRANSLATE = 'translate';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ConfigProviderInterface
     */
    protected $configProvider;

    /**
     * @var array
     */
    protected $excludeBlocks;

    /**
     * @var DomAppendElement[]
     */
    protected $appendElements;

    /**
     * @var string
     */
    protected $languageFrom;

    /**
     * @var string
     */
    protected $languageTo;

    /**
     * @var WordCollection
     */
    protected $words;

    /**
     * @var DomCheckerProvider
     */
    protected $domCheckerProvider;

    /**
     * Parser constructor.
     * @param Client $client
     * @param ConfigProviderInterface $config
     * @param array $excludeBlocks
     * @param DomAppendElement[] $appendElements
     */
    public function __construct(
        Client $client,
        ConfigProviderInterface $config,
        array $excludeBlocks = [],
        array $appendElements = []
    )
    {
        $this
            ->setClient($client)
            ->setConfigProvider($config)
            ->setExcludeBlocks($excludeBlocks)
            ->setAppendElements($appendElements)
            ->setWords(new WordCollection())
            ->setDomCheckerProvider(new DomCheckerProvider($this));
    }

    /**
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param array $excludeBlocks
     * @return $this
     */
    public function setExcludeBlocks(array $excludeBlocks)
    {
        $this->excludeBlocks = $excludeBlocks;

        return $this;
    }

    /**
     * @return array
     */
    public function getExcludeBlocks()
    {
        return $this->excludeBlocks;
    }

    /**
     * @param array $appendElements
     * @return $this
     * @internal param array $excludeBlocks
     */
    public function setAppendElements(array $appendElements)
    {
        $this->appendElements = $appendElements;

        return $this;
    }

    /**
     * @return array
     */
    public function getAppendElements()
    {
        return $this->appendElements;
    }

    /**
     * @param ConfigProviderInterface $config
     * @return $this
     */
    public function setConfigProvider(ConfigProviderInterface $config)
    {
        $this->configProvider = $config;

        return $this;
    }

    /**
     * @return ConfigProviderInterface
     */
    public function getConfigProvider()
    {
        return $this->configProvider;
    }

    /**
     * @param string $languageFrom
     * @return $this
     */
    public function setLanguageFrom($languageFrom)
    {
        $this->languageFrom = $languageFrom;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguageFrom()
    {
        return $this->languageFrom;
    }

    /**
     * @param string $languageTo
     * @return $this
     */
    public function setLanguageTo($languageTo)
    {
        $this->languageTo = $languageTo;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguageTo()
    {
        return $this->languageTo;
    }

    /**
     * @param WordCollection $wordCollection
     * @return $this
     */
    public function setWords(WordCollection $wordCollection)
    {
        $this->words = $wordCollection;

        return $this;
    }

    /**
     * @return WordCollection
     */
    public function getWords()
    {
        return $this->words;
    }

    /**
     * @param DomCheckerProvider $domCheckerProvider
     * @return $this
     */
    public function setDomCheckerProvider(DomCheckerProvider $domCheckerProvider)
    {
        $this->domCheckerProvider = $domCheckerProvider;
        return $this;
    }

    /**
     * @return DomCheckerProvider
     */
    public function getDomCheckerProvider()
    {
        return $this->domCheckerProvider;
    }

    /**
     * @param string $source
     * @param string $languageFrom
     * @param string $languageTo
     * @return string
     * @throws ApiError
     * @throws InputAndOutputCountMatchException
     * @throws InvalidWordTypeException
     * @throws MissingRequiredParamException
     * @throws MissingWordsOutputException
     */
    public function translate($source, $languageFrom, $languageTo)
    {
        // setters
        $this
            ->setLanguageFrom($languageFrom)
            ->setLanguageTo($languageTo);

        if ($this->client->getProfile()->getIgnoredNodes()) {
            $ignoredNodesFormatter = new IgnoredNodes($source);
            $source = $ignoredNodesFormatter->getSource();
        }
        $dom = \YaglotSimpleHtmlDom\str_get_html(
            $source,
            true,
            true,
            DEFAULT_TARGET_CHARSET,
            false
        );
        if ($dom === false) {
            return $source;
        }
        unset($source);

        // exclude blocks
        if (!empty($this->excludeBlocks)) {
            $excludeBlocks = new ExcludeBlocksFormatter($dom, $this->excludeBlocks);
            $dom = $excludeBlocks->getDom();
        }

        // checkers
        list($nodes, $jsons) = $this->checkers($dom);

        // api communication
        $translated = $this->apiTranslate($dom);

        // formatters
        $this->formatters($translated, $nodes, $jsons);

        $dom = $this->domAppendElements($dom);

        return $dom->save();
    }

    /**
     * @param string $source
     * @return string
     */
    public function htmlAppendElements($source) {
        try {
            $dom = \YaglotSimpleHtmlDom\str_get_html(
                $source,
                true,
                true,
                DEFAULT_TARGET_CHARSET,
                false
            );
            if ($dom === false) {
                return $source;
            }
            $dom = $this->domAppendElements($dom);

            return $dom->save();
        } catch (\Exception $exception) {
            return $source;
        }

    }

    protected function domAppendElements(simple_html_dom $dom) {
        foreach ($this->appendElements as $element) {
            $dom = DomAppend::appendTo(
                $dom,
                $element->getSelector(),
                $element->getContent()
            );
        }
        return $dom;
    }

    /**
     * @param simple_html_dom $dom
     * @return TranslateEntry
     * @throws ApiError
     * @throws InputAndOutputCountMatchException
     * @throws InvalidWordTypeException
     * @throws MissingRequiredParamException
     * @throws MissingWordsOutputException
     */
    protected function apiTranslate(simple_html_dom $dom)
    {
        // Translate endpoint parameters
        $params = [
            'from' => $this->getLanguageFrom(),
            'to' => $this->getLanguageTo()
        ];

        if ($this->getConfigProvider()->getAutoDiscoverTitle()) {
            $params['title'] = $this->getTitle($dom);
        }
        $params = array_merge($params, $this->getConfigProvider()->asArray());

        try {
            $translate = new TranslateEntry($params);
            $translate->setInputWords($this->getWords());
        } catch (\Exception $e) {
            $translate->setOutputWords($this->getWords());
        }


        $translate = new Translate($translate, $this->client);

        $translated = $translate->handle();

        return $translated;
    }

    /**
     * @param simple_html_dom $dom
     * @return string
     */
    protected function getTitle(simple_html_dom $dom)
    {
        $title = 'Empty title';
        foreach ($dom->find('title') as $k => $node) {
            if ($node->innertext != '') {
                $title = $node->innertext;
            }
        }
        return $title;
    }

    /**
     * @param $dom
     * @return array
     * @throws InvalidWordTypeException
     */
    protected function checkers($dom)
    {
        $nodes = $this->getDomCheckerProvider()->handle($dom);

        $checker = new JsonLdChecker($this, $dom);
        $jsons = $checker->handle();

        return [
            $nodes,
            $jsons
        ];
    }

    /**
     * @param TranslateEntry $translateEntry
     * @param array $nodes
     * @param array $jsons
     */
    protected function formatters(TranslateEntry $translateEntry, array $nodes, array $jsons)
    {
        $formatter = new DomFormatter($this, $translateEntry);
        $formatter->handle($nodes);

        $formatter = new JsonLdFormatter($this, $translateEntry, count($nodes));
        $formatter->handle($jsons);
    }
}
