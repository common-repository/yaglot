<?php

namespace Yaglot\Services;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Yaglot\Client\Api\LanguageEntry;
use Yaglot\Client\Endpoint\Languages;

class YaglotLanguagesService {


    /**
     * @return array
     */
    public function getLanguages() {

        $languages = [];

        foreach ($this->getLanguagesConfigured() as $language) {

            /**
             * @var LanguageEntry $language
             */
            $languages[$language->getIso639()] = $language->getEnglishName();
        }

        asort($languages);

        return $languages;
    }


    /**
     * @return \Yaglot\Client\Api\LanguageCollection
     */
    public function getLanguagesConfigured() {
        return(new Languages())->handle();
    }

}