<?php

namespace Yaglot\Parser\Check\Dom;

use Yaglot\Client\Api\Enum\WordType;

/**
 * Class LinkDataTooltip
 * @package Yaglot\Parser\Check\Dom
 */
class LinkDataTooltip extends AbstractDomChecker
{
    /**
     * {@inheritdoc}
     */
    const DOM = 'a';

    /**
     * {@inheritdoc}
     */
    const PROPERTY = 'data-tooltip';

    /**
     * {@inheritdoc}
     */
    const WORD_TYPE = WordType::TYPE_TEXT;
}
