<?php

namespace Yaglot\Client\Api\Exception;

/**
 * Class MissingRequiredParamException
 * @package Yaglot\Client\Api\Exception
 */
class MissingRequiredParamException extends AbstractException
{
    /**
     * MissingRequiredParamException constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'Required fields for $params are: from, to, bot, url.',
            YaglotCode::PARAMETERS
        );
    }
}
