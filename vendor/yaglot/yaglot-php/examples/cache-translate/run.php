<?php

require_once __DIR__. '/vendor/autoload.php';

use Yaglot\Client\Client;
use Yaglot\Client\Endpoint\Translate;
use Yaglot\Client\Api\Exception\MissingRequiredParamException;
use Yaglot\Client\Api\Exception\InvalidWordTypeException;
use Yaglot\Client\Api\Exception\InputAndOutputCountMatchException;
use Yaglot\Client\Api\Exception\ApiError;
use Predis\Client as Redis;
use Cache\Adapter\Predis\PredisCachePool;



// Caching you must install cache/predis-adapter
$redis = new Redis([
    'scheme' => getenv('REDIS_SCHEME'),
    'host'   => getenv('REDIS_HOST'),
    'port'   => getenv('REDIS_PORT'),
]);
$redisPool = new PredisCachePool($redis);


$client = new Client(
    $apiKey
);
$client->setCacheItemPool($redisPool);

$translate = new Translate($translate, $client);

try {
    $object = $translate->handle();
} catch (InvalidWordTypeException $e) {
    // input params types
    die($e->getMessage());
} catch (MissingRequiredParamException $e) {
    // input params issues
    die($e->getMessage());
} catch (InputAndOutputCountMatchException $e) {
    // api return doesn't contains same number of input & output words
    die($e->getMessage());
} catch (ApiError $e) {
    // api return error
    die($e->getMessage());
}

var_dump($object);
