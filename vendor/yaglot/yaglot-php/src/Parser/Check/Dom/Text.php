<?php

namespace Yaglot\Parser\Check\Dom;

use Yaglot\Client\Api\Enum\WordType;
use Yaglot\Util\Text as TextUtil;

/**
 * Class Text
 * @package Yaglot\Parser\Check
 */
class Text extends AbstractDomChecker
{
    /**
     * {@inheritdoc}
     */
    const DOM = 'text';

    /**
     * {@inheritdoc}
     */
    const PROPERTY = 'outertext';

    /**
     * {@inheritdoc}
     */
    const WORD_TYPE = WordType::TYPE_TEXT;

    /**
     * {@inheritdoc}
     */
    protected function check()
    {
        return ($this->node->parent()->tag != 'script'
            && $this->node->parent()->tag != 'style'
            && $this->node->parent()->tag != 'noscript'
            && $this->node->parent()->tag != 'code'
            && !is_numeric(TextUtil::fullTrim($this->node->outertext))
            && !preg_match('/^\d+%$/', TextUtil::fullTrim($this->node->outertext))
            && strpos($this->node->outertext, '[vc_') === false
            && strpos($this->node->outertext, '<?php') === false);
    }
}
