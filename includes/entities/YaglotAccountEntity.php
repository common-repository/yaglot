<?php

namespace Yaglot\Entities;

use Yaglot\Exceptions\ServerErrorException;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class YaglotAccountEntity {

    /**
     * @var int
     */
    public $id;


    /**
     * @var string
     */
    public $title;


    /**
     * YaglotAccountEntity constructor.
     * @param array $account
     * @throws ServerErrorException
     */
    public function __construct(array $account) {
        $this->setId($account)
            ->setTitle($account);
    }


    /**
     * @param array $account
     * @return $this
     * @throws ServerErrorException
     */
    public function setId(array $account) {

        if (!(isset($account['id']) && is_numeric($account['id']))) {
            throw new ServerErrorException('Missing or invalid account id.', 422);
        }

        $this->id = (int) $account['id'];

        return $this;
    }


    /**
     * @param array $account
     * @return $this
     * @throws ServerErrorException
     */
    public function setTitle(array $account) {

        if (!(isset($account['title']) && is_string($account['title']))) {
            throw new ServerErrorException('Missing or invalid account title.', 422);
        }

        $this->title = $account['title'];

        return $this;
    }

}