<?php

namespace Yaglot\Client\Endpoint;

use Yaglot\Client\Api\Exception\ApiError;
use Yaglot\Client\Api\Exception\InputAndOutputCountMatchException;
use Yaglot\Client\Api\Exception\InvalidWordTypeException;
use Yaglot\Client\Api\Exception\MissingRequiredParamException;
use Yaglot\Client\Api\Exception\MissingWordsOutputException;
use Yaglot\Client\Api\TranslateEntry;
use Yaglot\Client\Client;
use Yaglot\Client\Factory\Translate as TranslateFactory;

/**
 * Class Translate
 * @package Yaglot\Client\Endpoint
 */
class Translate extends Endpoint
{
    const METHOD = 'POST';
    const ENDPOINT = '/translate';

    /**
     * @var TranslateEntry
     */
    protected $translateEntry;

    protected $trimList = [];

    /**
     * Translate constructor.
     * @param TranslateEntry $translateEntry
     * @param Client $client
     */
    public function __construct(TranslateEntry $translateEntry, Client $client)
    {
        $this->setTranslateEntry($translateEntry);
        parent::__construct($client);
    }

    /**
     * @return TranslateEntry
     */
    public function getTranslateEntry()
    {
        return $this->translateEntry;
    }

    /**
     * @param TranslateEntry $translateEntry
     * @return $this
     */
    public function setTranslateEntry(TranslateEntry $translateEntry)
    {
        $this->translateEntry = $translateEntry;

        return $this;
    }

    /**
     * @return array
     */
    protected function beforeRequest()
    {
        // init
        $words = $this->getTranslateEntry()->getInputWords()->jsonSerialize();
        $requestWords = $cachedWords = $fullWords = [];

        $defaultParams = [
            'from' => $this->getTranslateEntry()->getParams('from'),
            'to' => $this->getTranslateEntry()->getParams('to')
        ];

        // fetch words to check if anything hit the cache
        foreach ($words as $key => $word) {

            // adding from & to languages to make key unique by language-pair
            $word = array_merge($word, $defaultParams);
            $cachedWord = $this->getCache()->getWithGenerate($word);

            // default behavior > sending word to request
            $where = 'request';
            $element = $word;
            $array = &$requestWords;

            // cached behavior > word is present in cache !
            if ($cachedWord->isHit()) {
                $where = 'cached';
                $element = $cachedWord->get();
                $array = &$cachedWords;
            }

            // get next element place
            $next = \count($array);

            // apply choosed behavior
            $array[$next] = $element;
            $fullWords[$key] = [
                'where' => $where,
                'place' => $next
            ];
        }

        return [
            $requestWords,
            $cachedWords,
            $fullWords
        ];
    }

    /**
     * @param array $response
     * @param array $beforeRequestResult
     * @return array
     */
    protected function afterRequest(array $response, array $beforeRequestResult)
    {
        // init
        list($requestWords, $cachedWords, $fullWords) = $beforeRequestResult;
        $fromWords = $toWords = [];

        $defaultParams = [
            'from' => $this->getTranslateEntry()->getParams('from'),
            'to' => $this->getTranslateEntry()->getParams('to')
        ];

        // fetch all words in one array
        foreach ($fullWords as $key => $details) {
            // if current word was in cache, just retrieve it
            if ($details['where'] === 'cached') {
                $fromWords[$key] = $cachedWords[$details['place']]['from'];
                $toWords[$key] = $cachedWords[$details['place']]['to'];
                continue;
            }

            // word was requested, let's retrieve data from response
            $word = $requestWords[$details['place']];
            $from = $response['from_words'][$details['place']];
            $to = $response['to_words'][$details['place']];

            // caching requested word
            $word = array_merge($word, $defaultParams);
            $cachedWord = $this->getCache()->getWithGenerate($word);

            $cachedWord->set([
                'from' => $from,
                'to' => $to
            ]);
            $this->getCache()->save($cachedWord);

            // then re-inject word inside
            $fromWords[$key] = $from;
            $toWords[$key] = $to;
        }

        $response['from_words'] = $fromWords;
        $response['to_words'] = $toWords;

        return $response;
    }

    /**
     * @return TranslateEntry
     * @throws ApiError
     * @throws InputAndOutputCountMatchException
     * @throws InvalidWordTypeException
     * @throws MissingRequiredParamException
     * @throws MissingWordsOutputException
     */
    public function handle()
    {
        $beforeRequest = [];
        $asArray = $this->translateEntry->jsonSerialize();



//        if ($this->getCache()->enabled()) {
//            $beforeRequest = $this->beforeRequest();
//            $asArray['words'] = $beforeRequest[0];
//        }

        list($rawBody, $httpStatusCode, $httpHeader) =
            $this->request(
                $this->trimSpaces($asArray),
                false
            );

        if ($httpStatusCode !== 200) {
            throw new ApiError($rawBody, $asArray);
        }

        $response = json_decode($rawBody, true);
//        if ($this->getCache()->enabled()) {
//            $response = $this->afterRequest($response, $beforeRequest);
//        }
        $response = $this->restoreSpaces($response);

        $factory = new TranslateFactory($response, $asArray);
        return $factory->handle();
    }


    public function trimSpaces($array) {
        $this->trimList = [];
        foreach ($array['words'] as $key => $word) {
            $this->trimList[$key] = [
                'before' => '',
                'after' => ''
            ];

            if(@preg_match('/^\s+/', $word['w'], $matches)) {
                if(isset($matches[0])) {
                    $this->trimList[$key]['before'] = $matches[0];
                }
            };
            if(@preg_match('/\s+$/', $word['w'], $matches)) {
                if(isset($matches[0])) {
                    $this->trimList[$key]['after'] = $matches[0];
                }
            };
            $array['words'][$key]['w'] = trim($word['w']);
        }
        return $array;
    }

    public function restoreSpaces($array) {
        foreach ($this->trimList as $key => $value) {
            if(isset($array[$key])) {
                $array[$key]['w'] = $value['before'] . $array[$key]['w'] . $value['after'];
            }
        }
        $this->trimList = [];
        return $array;
    }
}