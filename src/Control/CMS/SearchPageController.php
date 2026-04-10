<?php

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

namespace BimTheBam\Meilisearch\Control\CMS;

if (!class_exists(\PageController::class)) {
    return;
}

use BimTheBam\Meilisearch\Form\SearchForm;
use BimTheBam\Meilisearch\Model\CMS\SearchPage;
use SilverStripe\Forms\Form;
use SilverStripe\Model\ModelData;

/**
 * Class SearchPageController
 * @package BimTheBam\Meilisearch\Control\CMS
 * @mixin SearchPage
 */
class SearchPageController extends \PageController
{
    /**
     * @var array|string[]
     */
    private static array $allowed_actions = [
        'Form',
    ];

    /**
     * @return Form
     */
    public function Form(): Form
    {
        return SearchForm::create(
            $this,
            'Form',
        );
    }

    /**
     * @param array $data
     * @param SearchForm $form
     * @return ModelData
     */
    public function doSearch(array $data, SearchForm $form): ModelData
    {
        $form->loadDataFrom($data);

        return $this->customise([
            'Form' => $form,
            'IsSearch' => true,
        ]);
    }
}
