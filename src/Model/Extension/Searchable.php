<?php

namespace BimTheBam\Meilisearch\Model\Extension;

use BimTheBam\Meilisearch\Index;
use BimTheBam\Meilisearch\Model\Document;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use SilverStripe\ORM\DataExtension;
use Throwable;

/**
 * Class Searchable
 * @package BimTheBam\Meilisearch\Model\Extension
 */
class Searchable extends DataExtension
{
    /**
     * @return void
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws Throwable
     */
    public function onAfterWrite(): void
    {
        parent::onAfterWrite();

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
    public function onAfterDelete(): void
    {
        parent::onAfterDelete();

        Index::for_class($this->owner::class)?->remove(
            Document::create($this->owner)
        );
    }
}
