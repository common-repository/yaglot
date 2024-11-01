<?php

namespace Yaglot\Parser\Check\Dom;

use Yaglot\Client\Api\Enum\WordType;
use Yaglot\Util\Text as TextUtil;

/**
 * Class Placeholder
 * @package Yaglot\Parser\Check\Dom
 */
class Placeholder extends AbstractDomChecker
{
    /**
     * {@inheritdoc}
     */
    const DOM = 'input[type="text"],input[type="password"],input[type="search"],input[type="email"],textarea';

    /**
     * {@inheritdoc}
     */
    const PROPERTY = 'placeholder';

    /**
     * {@inheritdoc}
     */
    const WORD_TYPE = WordType::TYPE_PLACEHOLDER;

    /**
     * {@inheritdoc}
     */
    protected function check()
    {
        return (!is_numeric(TextUtil::fullTrim($this->node->placeholder))
            && !preg_match('/^\d+%$/', TextUtil::fullTrim($this->node->placeholder)));
    }
}
