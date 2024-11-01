<?php

namespace Yaglot\Parser\Check\Dom;

use Yaglot\Client\Api\Enum\WordType;

/**
 * Class SpanTitle
 * @package Yaglot\Parser\Check\Dom
 */
class SpanTitle extends AbstractDomChecker
{
    /**
     * {@inheritdoc}
     */
    const DOM = 'span[title]';

    /**
     * {@inheritdoc}
     */
    const PROPERTY = 'title';

    /**
     * {@inheritdoc}
     */
    const WORD_TYPE = WordType::TYPE_TEXT;
}
