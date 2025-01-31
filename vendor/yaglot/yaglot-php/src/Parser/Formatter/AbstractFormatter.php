<?php

namespace Yaglot\Parser\Formatter;

use Yaglot\Client\Api\TranslateEntry;
use Yaglot\Parser\Parser;

/**
 * Class AbstractFormatter
 * @package Yaglot\Parser\Formatter
 */
abstract class AbstractFormatter
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var TranslateEntry
     */
    protected $translated;

    /**
     * DomChecker constructor.
     * @param Parser $parser
     * @param TranslateEntry $translated
     */
    public function __construct(Parser $parser, TranslateEntry $translated)
    {
        $this
            ->setParser($parser)
            ->setTranslated($translated);
    }

    /**
     * @param Parser $parser
     * @return $this
     */
    public function setParser(Parser $parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * @return Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @param TranslateEntry $translated
     * @return $this
     */
    public function setTranslated(TranslateEntry $translated)
    {
        $this->translated = $translated;

        return $this;
    }

    /**
     * @return TranslateEntry
     */
    public function getTranslated()
    {
        return $this->translated;
    }

    /**
     * @param array $array
     * @return void
     */
    abstract public function handle(array $array);
}
