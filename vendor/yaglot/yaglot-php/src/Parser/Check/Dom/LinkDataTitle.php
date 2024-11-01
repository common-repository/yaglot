<?php

namespace Yaglot\Parser\Check\Dom;

use Yaglot\Client\Api\Enum\WordType;

/**
 * Class LinkDataTitle
 * @package Yaglot\Parser\Check\Dom
 */
class LinkDataTitle extends AbstractDomChecker
{
    /**
     * {@inheritdoc}
     */
    const DOM = 'a';

    /**
     * {@inheritdoc}
     */
    const PROPERTY = 'data-title';

    /**
     * {@inheritdoc}
     */
    const WORD_TYPE = WordType::TYPE_TEXT;

    /**
	 * {@inheritdoc}
	 */
	const ESCAPE_SPECIAL_CHAR =true;
}
