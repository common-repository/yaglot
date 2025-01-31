<?php

namespace Yaglot\Parser\Check\Dom;

use Yaglot\Client\Api\Enum\WordType;
use Yaglot\Util\Text as TextUtil;

/**
 * Class MetaContent
 * @package Yaglot\Parser\Check\Dom
 */
class MetaContent extends AbstractDomChecker
{
    /**
     * {@inheritdoc}
     */
    const DOM = 'meta[name="description"],meta[property="og:title"],meta[property="og:description"],meta[property="og:site_name"],meta[name="twitter:title"],meta[name="twitter:description"]';

    /**
     * {@inheritdoc}
     */
    const PROPERTY = 'content';

    /**
     * {@inheritdoc}
     */
    const WORD_TYPE = WordType::TYPE_META;

    /**
	 * {@inheritdoc}
	 */
    const ESCAPE_SPECIAL_CHAR = true;

    /**
     * {@inheritdoc}
     */
    protected function check()
    {
        return (!is_numeric(TextUtil::fullTrim($this->node->placeholder))
            && !preg_match('/^\d+%$/', TextUtil::fullTrim($this->node->placeholder)));
    }
}
