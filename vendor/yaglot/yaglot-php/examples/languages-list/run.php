<?php

require_once __DIR__. '/vendor/autoload.php';

use Yaglot\Client\Endpoint\Languages;

$languages = new Languages();

$languagesCollection = $languages->handle();

var_dump($languagesCollection);
