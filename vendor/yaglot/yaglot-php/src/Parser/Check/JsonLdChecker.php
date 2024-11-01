<?php

namespace Yaglot\Parser\Check;

use Yaglot\Client\Api\Exception\InvalidWordTypeException;
use Yaglot\Util\JsonLd;

/**
 * Class JsonLdChecker
 * @package Yaglot\Parser\Check
 */
class JsonLdChecker extends AbstractChecker
{
    /**
     * @return array
     * @throws InvalidWordTypeException
     */
    public function handle()
    {
        $jsons = [];

        foreach ($this->dom->find('script[type="application/ld+json"]') as $k => $row) {
            $json = json_decode($row->innertext, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = JsonLd::get($json, 'description');

                if ($value !== null) {
                    JsonLd::add($this->getParser()->getWords(), $value);
                    $jsons[] = [
                        'node' => $row,
                        'json' => $json
                    ];
                }
            }
        }

        return $jsons;
    }
}
