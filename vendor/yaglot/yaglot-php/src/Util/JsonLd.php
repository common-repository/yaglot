<?php

namespace Yaglot\Util;

use Yaglot\Client\Api\Enum\WordType;
use Yaglot\Client\Api\Exception\InvalidWordTypeException;
use Yaglot\Client\Api\WordCollection;
use Yaglot\Client\Api\WordEntry;

/**
 * Class JsonLd
 * @package Yaglot\Parser\Util
 */
class JsonLd
{
    /**
     * @param array $data
     * @param string $key
     * @return mixed
     */
    public static function get(array $data, $key)
    {
        if (isset($data[$key])) {
            return $data[$key];
        }
        return null;
    }

    /**
     * @param WordCollection $words
     * @param string $value
     * @throws InvalidWordTypeException
     */
    public static function add(WordCollection $words, $value)
    {
        $words->addOne(new WordEntry($value, WordType::TYPE_TEXT));
    }

    /**
     * @param WordCollection $words
     * @param mixed $data
     * @param null $index
     * @param int $nextJson
     * @return array
     */
    public static function set(WordCollection $words, $data, $index, &$nextJson)
    {
        $data[$index] = $words[$nextJson]->getWord();
        ++$nextJson;

        return $data;
    }
}
