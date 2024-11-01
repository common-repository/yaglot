<?php

namespace Yaglot\Client\Factory;

use Yaglot\Client\Api\Exception\InputAndOutputCountMatchException;
use Yaglot\Client\Api\Exception\InvalidWordTypeException;
use Yaglot\Client\Api\Exception\MissingRequiredParamException;
use Yaglot\Client\Api\Exception\MissingWordsOutputException;
use Yaglot\Client\Api\TranslateEntry;
use Yaglot\Client\Api\WordEntry;

/**
 * Class Translate
 * @package Yaglot\Client\Factory
 */
class Translate
{
    /**
     * @var array
     */
    protected $response = [];
    /**
     * @var array
     */
    protected $request = [];

    /**
     * Translate constructor.
     * @param array $response
     * @param array $request
     */
    public function __construct(array $response, array $request)
    {
        $this->setResponse($response)
            ->setRequest($request);
    }

    /**
     * @param array $response
     * @return $this
     */
    public function setResponse(array $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param array $request
     * @return $this
     * @internal param array $response
     */
    public function setRequest(array $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return array
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return TranslateEntry
     * @throws InputAndOutputCountMatchException
     * @throws InvalidWordTypeException
     * @throws MissingRequiredParamException
     * @throws MissingWordsOutputException
     */
    public function handle()
    {
        $response = $this->getResponse();
        $request = $this->getRequest();
        $params = [
            'from' => $request['from'],
            'to' => $request['to'],
            'url' => $request['url'],
            'title' => $request['title']
        ];
        $translate = new TranslateEntry($params);

//        if (!isset($response['to_words'])) {
//            throw new MissingWordsOutputException($response);
//        }
        if (count($request['words']) !== count($response)) {
            throw new InputAndOutputCountMatchException($response);
        }

        for ($i = 0; $i < \count($request['words']); ++$i) {
            $translate->getInputWords()->addOne(new WordEntry($request['words'][$i]['w'], $request['words'][$i]['t']));
        }
        for ($i = 0; $i < \count($response); ++$i) {
            $translate->getOutputWords()->addOne(new WordEntry($response[$i]['w'], $request['words'][$i]['t']));
        }

        return $translate;
    }
}
