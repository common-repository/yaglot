<?php

require_once __DIR__. '/vendor/autoload.php';

use Yaglot\Client\Client;
use Yaglot\Parser\Parser;
use Yaglot\Parser\ConfigProvider\ManualConfigProvider;

// Url of the page to be translated (used to group the translation results).
$url = "your-site.com/test-page";

$apiKey = getenv('YG_API_KEY');

$htmlContent = <<<HTML
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Content to translate</title>
</head>
<body>
  <h1>Hello world!</h1>
</body>
</html>
HTML;

$config = new ManualConfigProvider($url);

// Client
$client = new Client($apiKey);

$client->getProfile()->setIgnoredNodes(false);

// Example excluding nodes
$excludeNodes = [
    '.material-icons'
];

// Example append nodes
$appendNodes = [
    new \Yaglot\Util\Element\DomAppendElement(
        'body',
        '<h2>Hello world2 <- Append content not translated</h2>'
    )
];

$parser = new Parser($client, $config, $excludeNodes, $appendNodes);

$translatedContent = $parser->translate($htmlContent, 'en', 'de');

echo $translatedContent;
