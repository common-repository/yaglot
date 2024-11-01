<?php

namespace Yaglot\Client\Api\Shared;

/**
 * Trait AbstractCollectionSerializable
 * @package Yaglot\Client\Api\Shared
 */
trait AbstractCollectionSerializable
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $words = [];
        foreach ($this->collection as $entry) {
            $words[] = $entry->jsonSerialize();
        }

        return $words;
    }
}
