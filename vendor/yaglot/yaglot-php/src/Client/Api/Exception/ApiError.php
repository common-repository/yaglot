<?php

namespace Yaglot\Client\Api\Exception;

/**
 * Class ApiError
 * @package Yaglot\Client\Api\Exception
 */
class ApiError extends AbstractException
{
    /**
     * ApiError constructor.
     * @param $message
     * @param array $jsonBody
     */
    public function __construct($message, array $jsonBody = [])
    {
        parent::__construct($message, YaglotCode::AUTH, $jsonBody);
    }
}
