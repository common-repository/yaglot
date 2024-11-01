<?php

namespace Yaglot\Parser\ConfigProvider;

/**
 * Interface ConfigProviderInterface
 * @package Yaglot\Parser\ConfigProvider
 */
interface ConfigProviderInterface
{
    /**
     * @param null|string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return null|string
     */
    public function getTitle();

    /**
     * @param bool $autoDiscoverTitle
     * @return $this
     */
    public function setAutoDiscoverTitle($autoDiscoverTitle);

    /**
     * @return bool
     */
    public function getAutoDiscoverTitle();

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getUrl();


    /**
     * @return array
     */
    public function asArray();
}
