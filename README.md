# SilverStripe meilisearch module

## Intro

This module adds support for connecting with meilisearch as (multi-lingual) full-text search engine.

**[meilisearch](https://www.meilisearch.com/)**

> An open-source, lightning-fast, and hyper-relevant search engine that fits effortlessly into your workflow.

## Requirements

* SilverStripe Framework 6.x
* PHP 8.3
* meilisearch >= 1.3

### Optional

* [silverstripe/cms](https://github.com/silverstripe/silverstripe-cms) — enables the built-in SiteTree index, SearchPage page type, and automatic page search integration
* [silverstripe-fluent](https://github.com/tractorcow-farm/silverstripe-fluent) — enables multi-locale indexing with separate indexes per language

## Installation

`composer require bimthebam/silverstripe-meilisearch ^2.0`

## Configuration

### Environment variables

| Variable | Required | Description |
|---|---|---|
| `MEILISEARCH_HOST_AND_PORT` | Yes | URL of your meilisearch instance, e.g. `http://your-meilisearch-host:7700` |
| `MEILISEARCH_MASTER_KEY` | No | Authentication key, if the meilisearch instance requires it |
| `MEILISEARCH_INDEXES_PREFIX` | No | Prefix for all index names, useful for shared meilisearch instances |

## Usage

### Initialization

Run the built-in task [RebuildAllIndexesTask](src/Dev/Task/RebuildAllIndexesTask.php), which will create all the needed indexes within your meilisearch instance and fills them up with contents.

Although not necessary, it is suggested to run the task from CLI.

e.g. `sake tasks:meilisearch-rebuild-all-indexes`

### Search

When `silverstripe/cms` is installed, this module comes with a pre-defined index for SiteTree. So searching in page contents should mostly work out of the box.

To start, simply add a new page of type [SearchPage](src/Model/CMS/SearchPage.php) to your site tree.

Without `silverstripe/cms`, the module provides the core indexing and search infrastructure for use with custom indexes.

## Custom indexes

### 1. Create a DataObject

Define your DataObject and configure which fields meilisearch should use.

```php
namespace App\Model;

use SilverStripe\ORM\DataObject;

class Product extends DataObject
{
    private static string $table_name = 'Product';

    private static array $db = [
        'Title' => 'Varchar(255)',
        'Description' => 'HTMLText',
        'Price' => 'Currency',
        'Category' => 'Varchar(100)',
        'IsActive' => 'Boolean',
    ];

    private static array $meilisearch_searchable_fields = [
        'Title',
        'Description',
    ];

    private static array $meilisearch_filterable_fields = [
        'Category',
        'IsActive',
    ];

    private static array $meilisearch_sortable_fields = [
        'Title',
        'Price',
    ];
}
```

If `meilisearch_searchable_fields` is not defined, the module falls back to `Title` and `Content` if present.

Field values are automatically processed based on their type:

| Field type | Processing |
|---|---|
| `HTMLText`, `HTMLVarchar` | HTML tags are stripped |
| `Boolean` | Cast to `bool` |
| `Date`, `Time` | Converted to Unix timestamp |
| All others | Raw value |

### 2. Create an Index class

```php
namespace App\Search;

use App\Model\Product;
use BimTheBam\Meilisearch\Index;

class ProductIndex extends Index
{
    private static string $data_object_base_class = Product::class;

    private static array $excluded_classes = [];

    private static bool $check_can_view = false;
}
```

| Config | Description |
|---|---|
| `data_object_base_class` | The DataObject class this index handles. All subclasses are included automatically. |
| `excluded_classes` | Array of DataObject subclasses to exclude from the index. |
| `check_can_view` | If `true`, search results are filtered by `canView()` permissions. |

### 3. Apply the Searchable extension

For **non-versioned** DataObjects, apply the `Searchable` extension. This automatically updates the index on write and delete.

```yaml
# app/_config/meilisearch.yml
App\Model\Product:
  extensions:
    - BimTheBam\Meilisearch\Model\Extension\Searchable
```

For **versioned** DataObjects, use `SearchableVersioned` instead. This updates the index on publish/unpublish rather than on write.

```yaml
App\Model\Product:
  extensions:
    - BimTheBam\Meilisearch\Model\Extension\SearchableVersioned
```

### 4. Build and populate

Run `dev/build` to apply the extension, then rebuild the index:

```bash
sake dev/build
sake tasks:meilisearch-rebuild-all-indexes
```

After the initial rebuild, the index is kept in sync automatically via the extension hooks.

### 5. Searching programmatically

```php
use App\Search\ProductIndex;

$index = ProductIndex::create();
$results = $index->search('search term');

// Access results
foreach ($results->getList() as $result) {
    $product = $result->getRecord(); // Lazy-loads the DataObject
    echo $product->Title;
}

// Paginated results
$paginated = $results->getList(paginated: true, perPage: 10);
```

The `search()` method accepts additional parameters:

```php
$results = $index->search(
    q: 'search term',
    filter: ['Category = "Electronics"', 'IsActive = true'],
    limit: 50,
    sort: ['Price:asc'],
);
```

Filter and sort syntax follows the [meilisearch documentation](https://www.meilisearch.com/docs/learn/filtering_and_sorting/filter_search_results).

### Using the SearchForm

If you have multiple indexes, the built-in [SearchForm](src/Form/SearchForm.php) automatically offers a checkbox to let users choose which indexes to search in. Custom indexes are included alongside the SiteTree index (if available) without any additional configuration.

## ToDo

- [x] Add support for authentication keys
- [x] Complete documentation for custom indexes
