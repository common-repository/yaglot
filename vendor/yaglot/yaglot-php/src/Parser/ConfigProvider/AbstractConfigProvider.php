<?php

namespace Yaglot\Parser\ConfigProvider;

/**
 * Class AbstractConfigProvider
 * @package Yaglot\Parser\ConfigProvider
 */
abstract class AbstractConfigProvider implements ConfigProviderInterface
{
    /**
     * @var null|string
     */
    protected $title;

    /**
     * @var bool
     */
    protected $autoDiscoverTitle = true;

    /**
     * @var string
     */
    protected $url;


    /**
     * AbstractConfigProvider constructor.
     * @param string $url
     * @param null|string $title    Don't set this title if you want the Parser to parse title from DOM
     */
    public function __construct($url, $title = null)
    {
        $this
            ->setUrl($url)
            ->setTitle($title);
    }

    /**
     * If we put a null value into $title, we would force
     * the auto discover for the Parser.
     *
     * @param null|string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->setAutoDiscoverTitle($title === null);
        $this->title = $title;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setAutoDiscoverTitle($autoDiscoverTitle)
    {
        $this->autoDiscoverTitle = $autoDiscoverTitle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAutoDiscoverTitle()
    {
        return $this->autoDiscoverTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function asArray()
    {
        $data = [
            'url' => $this->getUrl()
        ];

        if (!$this->getAutoDiscoverTitle()) {
            $data['title'] = $this->getTitle();
        }

        return $data;
    }
}
