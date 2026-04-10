<?php

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

namespace BimTheBam\Meilisearch\Model\CMS;

if (!class_exists(\Page::class)) {
    return;
}

use BimTheBam\Meilisearch\Control\CMS\SearchPageController;

/**
 * Class SearchPage
 * @package BimTheBam\Meilisearch\Model\CMS
 */
class SearchPage extends \Page
{
    /**
     * @var string
     */
    private static string $table_name = 'MeilisearchSearchPage';

    /**
     * @var string
     */
    private static string $singular_name = 'Search page';

    /**
     * @var string
     */
    private static string $plural_name = 'Search pages';

    /**
     * @var string
     */
    private static string $controller_name = SearchPageController::class;
}
