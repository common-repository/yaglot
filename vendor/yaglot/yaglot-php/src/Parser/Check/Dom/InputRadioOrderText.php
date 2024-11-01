<?php

namespace Yaglot\Parser\Check\Dom;

use Yaglot\Client\Api\Enum\WordType;

/**
 * Class InputRadioOrderText
 * @package Yaglot\Parser\Check\Dom
 */
class InputRadioOrderText extends AbstractDomChecker
{
    /**
     * {@inheritdoc}
     */
    const DOM = 'input[type="radio"]';

    /**
     * {@inheritdoc}
     */
    const PROPERTY = 'data-order_button_text';

    /**
     * {@inheritdoc}
     */
    const WORD_TYPE = WordType::TYPE_BUTTON;
}
