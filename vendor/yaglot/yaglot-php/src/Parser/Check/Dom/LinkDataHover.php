<?php

namespace Yaglot\Parser\Check\Dom;

use Yaglot\Client\Api\Enum\WordType;

/**
 * Class LinkDataHover
 * @package Yaglot\Parser\Check\Dom
 */
class LinkDataHover extends AbstractDomChecker
{
    /**
     * {@inheritdoc}
     */
    const DOM = 'a';

    /**
     * {@inheritdoc}
     */
    const PROPERTY = 'data-hover';

    /**
     * {@inheritdoc}
     */
    const WORD_TYPE = WordType::TYPE_TEXT;
}
