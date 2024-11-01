<?php

namespace Yaglot\Client\Api\Shared;

/**
 * Trait AbstractCollectionCountable
 * @package Yaglot\Client\Api\Shared
 */
trait AbstractCollectionCountable
{
    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return \count($this->collection);
    }
}
