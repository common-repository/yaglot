<?php

namespace Yaglot\Parser\Check\Dom;

use Yaglot\Client\Api\Enum\WordType;
use Yaglot\Util\Text as TextUtil;

/**
 * Class IframeSrc
 * @package Yaglot\Parser\Check\Dom
 */
class IframeSrc extends AbstractDomChecker
{
    /**
     * {@inheritdoc}
     */
    const DOM = 'iframe';

    /**
     * {@inheritdoc}
     */
    const PROPERTY = 'src';

    /**
     * {@inheritdoc}
     */
    const WORD_TYPE = WordType::TYPE_IFRAME_SRC;

    /**
     * {@inheritdoc}
     */
    protected function check()
    {
        return TextUtil::contains(TextUtil::fullTrim($this->node->src), '.youtube.');
    }
}
