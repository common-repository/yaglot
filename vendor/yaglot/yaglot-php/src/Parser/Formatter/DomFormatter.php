<?php

namespace Yaglot\Parser\Formatter;

use Yaglot\Parser\Check\Dom\ImageSource;
use Yaglot\Parser\Check\Dom\MetaContent;

/**
 * Class DomFormatter
 * @package Yaglot\Parser\Formatter
 */
class DomFormatter extends AbstractFormatter
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $nodes)
    {
        $translated_words = $this->getTranslated()->getOutputWords();

        for ($i = 0; $i < \count($nodes); ++$i) {
            $currentNode = $nodes[$i];

            if ($translated_words[$i] !== null) {
                $currentTranslated = $translated_words[$i]->getWord();

                $this->metaContent($currentNode, $currentTranslated);
                $this->imageSource($currentNode, $currentTranslated, $i);
            }
        }
    }

    /**
     * @param array $details
     * @param string $translated
     */
    protected function metaContent(array $details, $translated) {
        $property = $details['property'];

        if ($details['class']::ESCAPE_SPECIAL_CHAR) {
            $details['node']->$property = htmlspecialchars($translated);
        } else {
            $details['node']->$property = $translated;
        }
    }

    protected function imageSource(array $details, $translated, $index) {
        $words = $this->getTranslated()->getInputWords();

        if ($details['class'] === '\Yaglot\Parser\Check\Dom\ImageSource') {
            $details['node']->src = $translated;
            if ($details['node']->hasAttribute('srcset') &&
                $details['node']->srcset != '' &&
                $translated != $words[$index]->getWord()) {
                $details['node']->srcset = '';
            }
        }
    }
}
