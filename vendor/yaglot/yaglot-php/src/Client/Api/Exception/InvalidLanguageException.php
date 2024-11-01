<?php

namespace Yaglot\Client\Api\Exception;

/**
 * Class InvalidLanguageException
 * @package Yaglot\Client\Api\Exception
 */
class InvalidLanguageException extends AbstractException
{
    /**
     * InvalidLanguageException constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'The given language is invalid.',
            YaglotCode::PARAMETERS
        );
    }
}
