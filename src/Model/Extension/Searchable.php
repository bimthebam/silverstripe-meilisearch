<?php

namespace BimTheBam\Meilisearch\Model\Extension;

use BimTheBam\Meilisearch\Index;
use BimTheBam\Meilisearch\Model\Document;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use SilverStripe\Core\Extension;
use Throwable;

/**
 * Class Searchable
 * @package BimTheBam\Meilisearch\Model\Extension
 */
class Searchable extends Extension
{
    /**
     * @return void
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws Throwable
     */
    protected function onAfterWrite(): void
    {
        Index::for_class($this->owner::class)?->add(
            Document::create($this->owner)
        );
    }

    /**
     * @return void
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws Throwable
     */
    protected function onAfterDelete(): void
    {
        Index::for_class($this->owner::class)?->remove(
            Document::create($this->owner)
        );
    }
}
