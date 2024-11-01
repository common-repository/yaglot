<?php

namespace Yaglot\Client\Api\Exception;

/**
 * Class InvalidWordTypeException
 * @package Yaglot\Client\Api\Exception
 */
class InvalidWordTypeException extends AbstractException
{
    /**
     * InvalidWordTypeException constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'The given WordType is invalid.',
            YaglotCode::PARAMETERS
        );
    }
}
