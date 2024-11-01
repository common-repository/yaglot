<?php

namespace Yaglot\Parser\Check\Dom;

use Yaglot\Client\Api\Enum\WordType;
use Yaglot\Util\Text as TextUtil;

/**
 * Class Button
 * @package Yaglot\Parser\Check\Dom
 */
class Button extends AbstractDomChecker
{
    /**
     * {@inheritdoc}
     */
    const DOM = 'input[type="submit"],input[type="button"],input[type="reset"]';

    /**
     * {@inheritdoc}
     */
    const PROPERTY = 'value';

    /**
     * {@inheritdoc}
     */
    const WORD_TYPE = WordType::TYPE_BUTTON;

    /**
     * {@inheritdoc}
     */
    protected function check()
    {
        return (!is_numeric(TextUtil::fullTrim($this->node->value))
            && !preg_match('/^\d+%$/', TextUtil::fullTrim($this->node->value)));
    }
}
