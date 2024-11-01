<?php

namespace Yaglot\Integrations\Woocommerce;

use Yaglot\Client\Api\Enum\BotType;
use Yaglot\Client\Api\Enum\WordType;
use Yaglot\Client\Api\TranslateEntry;
use Yaglot\Client\Api\WordEntry;
use Yaglot\Client\Client;
use Yaglot\Client\Endpoint\Translate;
use Yaglot\Helpers\JsonInlineHelper;
use Yaglot\Services\YaglotOptionsService;
use Yaglot\Services\YaglotRequestUrlService;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotWcTranslate {


    /**
     * @var YaglotOptionsService
     */
    private $optionsService;


    /**
     * @var YaglotRequestUrlService
     */
    private $requestUrlService;


    /**
     * @var string
     */
    private $originalLanguage;


    /**
     * @var string
     */
    private $currentLanguage;


    /**
     * YaglotWcTranslate constructor.
     * @param YaglotOptionsService $optionsService
     * @param YaglotRequestUrlService $requestUrlService
     * @param string $originalLanguage
     * @param string $currentLanguage
     */
    public function __construct(YaglotOptionsService $optionsService,
                                YaglotRequestUrlService $requestUrlService,
                                $originalLanguage,
                                $currentLanguage) {

        $this->optionsService = $optionsService;
        $this->requestUrlService = $requestUrlService;
        $this->originalLanguage = $originalLanguage;
        $this->currentLanguage = $currentLanguage;
    }

    /**
     * @param array $all_words
     * @return TranslateEntry
     * @throws \Yaglot\Client\Api\Exception\ApiError
     * @throws \Yaglot\Client\Api\Exception\InputAndOutputCountMatchException
     * @throws \Yaglot\Client\Api\Exception\InvalidWordTypeException
     * @throws \Yaglot\Client\Api\Exception\MissingRequiredParamException
     * @throws \Yaglot\Client\Api\Exception\MissingWordsOutputException
     */
    protected function translateEntries($all_words) {

        $params = [
            'from' => $this->originalLanguage,
            'to'   => $this->currentLanguage,
            'url'  => $this->requestUrlService->getFullUrl(),
            'bot'  => BotType::HUMAN,
        ];

        $translate = new TranslateEntry($params);

        $word_collection = $translate->getInputWords();
        foreach ($all_words as $value) {
            $value = JsonInlineHelper::formatForApi($value);
            $word_collection->addOne(new WordEntry($value, WordType::TYPE_TEXT));
        }

        $client = new Client($this->optionsService->getApiKey());
        $translate = new Translate($translate, $client);
        return $translate->handle();
    }

    /**
     * @param string $content
     * @return string
     */
    protected function translateAdresseI18n($content) {

        preg_match('#wc_address_i18n_params(.*?);#', $content, $match);

        if (!isset($match[1])) {
            return $content;
        }

        preg_match_all('#(label|placeholder)\\\":\\\"(.*?)\\\"#', $match[1], $all);

        try {
            $object = $this->translateEntries($all[2]);
        } catch (\Exception $e) {
            return $content;
        }

        foreach ($object->getInputWords() as $key => $input_word) {
            $from_input = JsonInlineHelper::unformatFromApi($input_word->getWord());
            $to_output = JsonInlineHelper::unformatFromApi($object->getOutputWords()[$key]->getWord());

            $content = str_replace('\"' . $from_input . '\"', '\"' . $to_output . '\"', $content);
        }

        return $content;
    }


    /**
     * @param string $content
     * @return string
     */
    protected function translateAddToCartParams($content) {

        preg_match('#wc_add_to_cart_params(.*?);#', $content, $match);

        if (!isset($match[1])) {
            return $content;
        }

        preg_match_all('#i18n_view_cart\":\"(.*?)\"#', $match[1], $all);

        try {
            $object = $this->translateEntries($all[1]);
        } catch (\Exception $e) {
            return $content;
        }


        foreach ($object->getInputWords() as $key => $input_word) {
            $from_input = JsonInlineHelper::unformatFromApi($input_word->getWord());
            $to_output = JsonInlineHelper::unformatFromApi($object->getOutputWords()[$key]->getWord());

            $content = str_replace('"' . $from_input . '"', '"' . $to_output . '"', $content);
        }

        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    public function translateWords($content) {

        $content = $this->translateAdresseI18n($content);
        $content = $this->translateAddToCartParams($content);

        return $content;
    }
}
