<?php

namespace Yaglot\Parser\Check\Dom;

use Yaglot\Client\Api\Enum\WordType;

/**
 * Class ImageAlt
 * @package Yaglot\Parser\Check\Dom
 */
class ImageAlt extends AbstractDomChecker
{
    /**
     * {@inheritdoc}
     */
    const DOM = 'img';

    /**
     * {@inheritdoc}
     */
    const PROPERTY = 'alt';

    /**
     * {@inheritdoc}
     */
    const WORD_TYPE = WordType::TYPE_IMG_ALT;

    /**
	 * {@inheritdoc}
	 */
	const ESCAPE_SPECIAL_CHAR =true;
}
