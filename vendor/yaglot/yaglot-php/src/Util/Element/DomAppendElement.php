<?php
/**
 * Created by PhpStorm.
 * User: taras.developer
 * Date: 30.10.2018
 * Time: 11:59
 */

namespace Yaglot\Util\Element;


class DomAppendElement
{
    /**
     * @var string CSS selector
     */
    protected $selector = '';

    /**
     * @var string HTML Content
     */
    protected $content = '';

    /**
     * DomAppendElement constructor.
     * @param string $selector
     * @param string $content
     */
    public function __construct($selector, $content)
    {
        $this->setSelector($selector)
            ->setContent($content);
    }

    /**
     * @param string $selector
     * @return $this
     */
    public function setSelector($selector) {
        $this->selector = $selector;
        return $this;
    }

    /**
     * @return string
     */
    public function getSelector() {
        return $this->selector;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }



}