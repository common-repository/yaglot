<?php

namespace Yaglot\Client\Api\Shared;

/**
 * Interface AbstractCollectionInterface
 * @package Yaglot\Client\Api\Shared
 */
interface AbstractCollectionInterface
{
    /**
     * Add one word at a time
     *
     * @param AbstractCollectionEntry $entry
     */
    public function addOne(AbstractCollectionEntry $entry);

    /**
     * Add several words at once
     *
     * @param AbstractCollectionEntry[] $entries
     */
    public function addMany(array $entries);
}
