<?php

namespace Yaglot\Parser\Check\Dom;

use Yaglot\Client\Api\Enum\WordType;

/**
 * Class IframeSrc
 * @package Yaglot\Parser\Check\Dom
 */
class AudioSrc extends AbstractDomChecker
{
    /**
     * {@inheritdoc}
     */
    const DOM = 'audio';

    /**
     * {@inheritdoc}
     */
    const PROPERTY = 'src';

    /**
     * {@inheritdoc}
     */
    const WORD_TYPE = WordType::TYPE_AUDIO;
    
}
