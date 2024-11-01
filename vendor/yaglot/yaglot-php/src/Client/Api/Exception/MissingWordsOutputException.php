<?php

namespace Yaglot\Client\Api\Exception;

/**
 * Class MissingWordsOutputException
 * @package Yaglot\Client\Api\Exception
 */
class MissingWordsOutputException extends \Exception
{
    /**
     * MissingWordsOutputException constructor.
     * @param array $jsonBody
     */
    public function __construct(array $jsonBody)
    {
        parent::__construct(
            'There is no output words.',
            YaglotCode::GENERIC,
            $jsonBody
        );
    }
}
