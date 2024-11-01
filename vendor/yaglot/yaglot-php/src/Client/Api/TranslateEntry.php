<?php

namespace Yaglot\Client\Api;

use JsonSerializable;
use Yaglot\Client\Api\Exception\MissingRequiredParamException;

if (!function_exists('array_keys_exists')) {
    /**
     * Used to check if multiple keys are defined in given array
     *
     * @param array $keys
     * @param array $arr
     * @return bool
     */
    function array_keys_exists(array $keys, array $arr)
    {
        return !array_diff_key(array_flip($keys), $arr);
    }
}

/**
 * Class TranslateEntry
 * @package Yaglot\Client\Api
 */
class TranslateEntry implements JsonSerializable
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @var WordCollection
     */
    protected $inputWords;

    /**
     * @var WordCollection
     */
    protected $outputWords;

    /**
     * TranslateEntry constructor.
     * @param array $params                     Params of the translate entry, required fields: from, to, bot, request_url & optional: title ("Empty title" by default)
     * @param WordCollection|null $words        Collection of words
     * @throws MissingRequiredParamException    If params are missing we throw this exception
     */
    public function __construct(array $params, WordCollection $words = null)
    {
        $this->setParams($params)
            ->setInputWords($words)
            ->setOutputWords();
    }

    /**
     * Default params values
     *
     * @return array
     */
    protected function defaultParams()
    {
        return [
            'title' => 'Not used for now'
        ];
    }

    /**
     * Required params field names
     *
     * @return array
     */
    protected function requiredParams()
    {
        return [
            'from',
            'to',
            'url'
        ];
    }

    /**
     * @param null|string $key
     * @return array|bool|mixed
     */
    public function getParams($key = null)
    {
        if ($key !== null) {
            if (isset($this->params[$key])) {
                return $this->params[$key];
            }
            return false;
        }

        return $this->params;
    }

    /**
     * @param array $params
     * @return $this
     * @throws MissingRequiredParamException    If params are missing we throw this exception
     */
    public function setParams(array $params)
    {
        // merging default params with user params
        $this->params = array_merge($this->defaultParams(), $params);

        if (!array_keys_exists($this->requiredParams(), $this->params)) {
            throw new MissingRequiredParamException();
        }

        return $this;
    }

    /**
     * @return WordCollection
     */
    public function getInputWords()
    {
        return $this->inputWords;
    }

    /**
     * Used to fill input words collection
     * If $words is null, it would put an empty word collection
     *
     * @param WordCollection|null $words
     * @return $this
     */
    public function setInputWords($words = null)
    {
        if ($words === null) {
            $this->inputWords = new WordCollection();
        } else {
            $this->inputWords = $words;
        }

        return $this;
    }

    /**
     * @return WordCollection
     */
    public function getOutputWords()
    {
        return $this->outputWords;
    }

    /**
     * Used to fill output words collection
     * If $words is null, it would put an empty word collection
     *
     * @param WordCollection|null $words
     * @return $this
     */
    public function setOutputWords($words = null)
    {
        if ($words === null) {
            $this->outputWords = new WordCollection();
        } else {
            $this->outputWords = $words;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'from' => $this->params['from'],
            'to' => $this->params['to'],
            'title' => $this->params['title'],
            'url' => $this->params['url'],
            'words' => $this->inputWords->jsonSerialize()
        ];
    }
}
