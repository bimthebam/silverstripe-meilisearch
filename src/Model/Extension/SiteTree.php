<?php

namespace BimTheBam\Meilisearch\Model\Extension;

use BimTheBam\Meilisearch\Index;
use BimTheBam\Meilisearch\Model\Document;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use Throwable;

/**
 * Class SiteTree
 * @package BimTheBam\Meilisearch\Model\Extension
 * @mixin \SilverStripe\CMS\Model\SiteTree
 * @property \SilverStripe\CMS\Model\SiteTree $owner
 */
class SiteTree extends SearchableVersioned
{
    /**
     * @return void
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws Throwable
     */
    protected function onAfterPublish(): void
    {
        if ($this->owner->ShowInSearch) {
            parent::onAfterPublish();
        } else {
            Index::for_class($this->owner::class)?->remove(
                Document::create($this->owner)
            );
        }
    }

    /**
     * @return void
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws Throwable
     */
    protected function onAfterWrite(): void
    {
        if (!$this->owner->ShowInSearch) {
            Index::for_class($this->owner::class)?->remove(
                Document::create($this->owner)
            );
        }
    }
}
