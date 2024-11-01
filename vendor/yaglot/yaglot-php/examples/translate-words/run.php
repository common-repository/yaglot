<?php

require_once __DIR__ . '/vendor/autoload.php';

use Yaglot\Client\Api\Exception\ApiError;
use Yaglot\Client\Client;
use Yaglot\Client\Endpoint\Translate;
use Yaglot\Client\Api\Exception\MissingRequiredParamException;
use Yaglot\Client\Api\Exception\InvalidWordTypeException;
use Yaglot\Client\Api\Exception\InputAndOutputCountMatchException;
use Yaglot\Client\Api\TranslateEntry;
use Yaglot\Client\Api\WordEntry;
use Yaglot\Client\Api\Enum\WordType;

// TranslateEntry
$params = [
    'from' => 'en',
    'to' => 'de',
    'url' => 'your-site.com/test-page', // Url of the page to be translated (used to group the translation results).
];

$apiKey = getenv('YG_API_KEY');

try {
    $translate = new TranslateEntry($params);
    $translate->getInputWords()
        ->addOne(new WordEntry('My first word.', WordType::TYPE_TEXT))
        ->addOne(new WordEntry('My second word', WordType::TYPE_TEXT));
} catch (InvalidWordTypeException $e) {
    // input params issues, WordType on WordEntry construct needs to be valid
    die($e->getMessage());
} catch (MissingRequiredParamException $e) {
    // input params issues, just need to have required fields
    die($e->getMessage());
}

// Client
$client = new Client(
    $apiKey
);

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
