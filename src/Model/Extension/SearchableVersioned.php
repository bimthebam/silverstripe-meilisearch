<?php

namespace BimTheBam\Meilisearch\Model\Extension;

use BimTheBam\Meilisearch\Index;
use BimTheBam\Meilisearch\Model\Document;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use SilverStripe\Core\Extension;
use Throwable;

/**
 * Class SearchableVersioned
 * @package BimTheBam\Meilisearch\Model\Extension
 */
class SearchableVersioned extends Extension
{
    /**
     * @return void
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws Throwable
     */
    protected function onAfterPublish(): void
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
    protected function onAfterUnpublish(): void
    {
        Index::for_class($this->owner::class)?->remove(
            Document::create($this->owner)
        );
    }
}
