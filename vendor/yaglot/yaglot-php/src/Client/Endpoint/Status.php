<?php

namespace Yaglot\Client\Endpoint;

use Yaglot\Client\Api\Exception\ApiError;

/**
 * Class Status
 * @package Yaglot\Client\Endpoint
 */
class Status extends Endpoint
{
    const METHOD = 'GET';
    const ENDPOINT = '/client/info';

    /**
     * @return bool
     * @throws ApiError
     * @see
     */
    public function handle()
    {
        list($rawBody, $httpStatusCode, $httpHeader, $array) = $this->request([], false);
        if ($httpStatusCode === 200) {
            if(isset($array['data'])) {
                return $array['data'];
            } else {
                throw new ApiError('Wrong response format', $array);
            }
        } else {
            if(isset($array['message'])) {
                throw new ApiError($array['message'], $array);
            } else {
                throw new ApiError($rawBody, []);
            }
        }
    }
}
