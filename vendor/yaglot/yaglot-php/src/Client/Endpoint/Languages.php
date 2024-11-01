<?php

namespace Yaglot\Client\Endpoint;

use Yaglot\Client\Api\LanguageCollection;
use Yaglot\Client\Client;
use Yaglot\Client\Factory\Languages as LanguagesFactory;

/**
 * Class Languages
 * @package Yaglot\Client\Endpoint
 */
class Languages extends Endpoint
{
    const METHOD = 'GET';
    const ENDPOINT = '/languages';

    public function __construct()
    {
        parent::__construct(new Client('Not need on this endpoint'));
    }

    /**
     * @return LanguageCollection
     */
    public function handle()
    {
        $languageCollection = new LanguageCollection();
        $data = LanguagesFactory::data();
        foreach ($data as $language) {
            $factory = new LanguagesFactory($language);
            $languageCollection->addOne($factory->handle());
        }

        return $languageCollection;
    }
}
