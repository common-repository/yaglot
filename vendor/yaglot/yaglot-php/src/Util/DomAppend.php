<?php

namespace Yaglot\Util;

use function YaglotSimpleHtmlDom\dump_html_tree;
use YaglotSimpleHtmlDom\simple_html_dom;
use YaglotSimpleHtmlDom\simple_html_dom_node;

/**
 * Class JsonLd
 * @package Yaglot\Parser\Util
 */
class DomAppend
{
    /**
     * @param simple_html_dom $dom
     * @param string $selector
     * @param string $content
     * @return simple_html_dom
     */
    public static function appendTo(simple_html_dom $dom, $selector, $content) {

        $contentNode = \YaglotSimpleHtmlDom\str_get_html($content);

        /**
         * @var $row simple_html_dom_node
         */
        foreach ($dom->find($selector) as $k => $row) {
            $row->appendChild($contentNode->root);
            // Append only in one element
            break;
        }

        return $dom;
    }

}
