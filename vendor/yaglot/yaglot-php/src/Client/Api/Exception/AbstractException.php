<?php

namespace Yaglot\Client\Api\Exception;

use Exception;

/**
 * Class AbstractException
 * @package Yaglot\Client\Api\Exception
 */
abstract class AbstractException extends Exception
{
    /**
     * @var int
     */
    protected $yaglotCode;

    /**
     * @var array
     */
    protected $jsonBody;

    /**
     * AbstractException constructor.
     * @param string $message
     * @param int $yaglotCode
     * @param array $jsonBody
     */
    public function __construct($message, $yaglotCode = YaglotCode::GENERIC, $jsonBody = [])
    {
        parent::__construct($message);

        $this->yaglotCode = $yaglotCode;
        $this->jsonBody = $jsonBody;
    }

    /**
     * @return int
     */
    public function getYaglotCode()
    {
        return $this->yaglotCode;
    }

    /**
     * @return array
     */
    public function getJsonBody()
    {
        return $this->jsonBody;
    }

    /**
     * @return int
     */
    public function getApiCode()
    {
        if(isset($this->jsonBody['code'])) {
            return (int)$this->jsonBody['code'];
        }
        return 0;
    }

    /**
     * @return string
     */
    public function getApiMessage()
    {
        if(isset($this->jsonBody['message'])) {
            return $this->jsonBody['message'];
        }
        return '';
    }

    /**
     * @return mixed
     */
    public function getApiErrors()
    {
        if(isset($this->jsonBody['errors'])) {
            return $this->jsonBody['errors'];
        }
        return [];
    }
}
