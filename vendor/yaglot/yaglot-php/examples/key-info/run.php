<?php

require_once __DIR__. '/vendor/autoload.php';

use Yaglot\Client\Api\Exception\ApiError;
use Yaglot\Client\Client;
use Yaglot\Client\Endpoint\Status;

$apiKey = getenv('YG_API_KEY');

// Client
$client = new Client(
    $apiKey,
    [
        'requestTarget' => 'dashboard'
    ]
);

$status = new Status($client);

try {
    $statusData = $status->handle();
} catch (ApiError $e) {
    // Api response error
    die($e->getMessage());
} catch (\Exception $e) {
    // Unexpected error
    die($e->getMessage());
}

var_dump($statusData);
