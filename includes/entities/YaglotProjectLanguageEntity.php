<?php

namespace Yaglot\Entities;

use Yaglot\Exceptions\ServerErrorException;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotProjectLanguageEntity {

    /**
     * @var string
     */
    public $from;


    /**
     * @var string
     */
    public $to;


    /**
     * YaglotProjectLanguageEntity constructor.
     * @param array $language
     * @throws ServerErrorException
     */
    public function __construct(array $language) {

        $this->setFrom($language)
            ->setTo($language);
    }


    /**
     * @param array $language
     * @return $this
     * @throws ServerErrorException
     */
    public function setFrom(array $language) {

        if (!(isset($language['from']) && is_string($language['from']))) {
            throw new ServerErrorException('Missing or invalid language from.', 422);
        }

        $this->from = $language['from'];

        return $this;
    }


    /**
     * @param array $language
     * @return $this
     * @throws ServerErrorException
     */
    public function setTo(array $language) {

        if (!(isset($language['to']) && is_string($language['to']))) {
            throw new ServerErrorException('Missing or invalid language to.', 422);
        }

        $this->to = $language['to'];

        return $this;
    }
}