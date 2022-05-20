# smart-search-php

Search/filter using simple Google-style search strings to perform complex filter operations.

(PHP backend equivalent of "smart-search-filter" JS library)

## Installation:

Add this library to your project's `composer.json` file:

```json
{
    "require": {
        ...
        "miking7/smart-search-php": "^1.0"
    }
    ...

    "repositories": {
        "smart-search-php": {
            "type": "vcs",
            "url": "https://github.com/miking7/smart-search-php"
        }
    }
}
```

...then install using the following commands:

```bash
composer update miking7/smart-search-php
```

## Support for whereNot():

Note: If using Laravel 8 (vs Laravel >=9), support for the **whereNot()** function needs to be provided using the following package: https://github.com/protonemedia/laravel-eloquent-where-not.

```bash
composer require protonemedia/laravel-eloquent-where-not
```

...and register the macro as described in their [installation instructions ](https://github.com/protonemedia/laravel-eloquent-where-not#installation).

## Basic Usage Examples:

The examples below demonstrate:

* How to initialise the filter class
* Debug info - showing  a description of the filter operations
* `getSqlFilter()` - SQL where-clause-based database filtering
* `getBuilderFilter` - Laravel Builder-based database filtering
* `filterArray()` - array-based filtering
* `filterCollection()` - Laravel Collection-based filtering

Examples:
```php
use Miking7\SmartSearch\SmartSearch;

# Sample search string:
$searchPhrase = 'tags:connecting series:"tassie encounters" -health';
#   The 'tags' field contains the word 'connecting'
#   The 'series' field contains the phrase 'tassie encounters'
#   ...and NO fields contain the word 'health'

# Initialise the filter and parse the search phrase
$sqlEscapeStringFn = fn($str) => DB::connection()->getPdo()->quote($str);  // If you use SQL-based filtering (below), you need to provide access to your relevant SQL-injection-safe function - ie: mysqli_real_escape_string() / PDO::quote()
$smartSearch = new SmartSearch('file|series|content|guests|tags', '', [], $sqlEscapeStringFn);
$smartSearch->parse($search);
 
# Debug info
var_dump($smartSearch->errors);
var_dump($smartSearch->getFilterOpsDescription());

# Example for SQL-based filtering:
$whereClause = $smartSearch->getSqlFilter());
$sql = “SELECT * FROM content WHERE ... AND “. $whereClause;
 
# Example for Laravel Query Builder-based filtering:
$data = MyModel::where($smartSearch->getBuilderFilter())->get();

# Example for array-based filtering:
$items = [
    {
        "file": "2021-05-20 SFC.mp3",
        "series": "Tassie Encounters",
        "content": "Searching for Certainty: Does My Life Matter",
        "guests": "Joe Bloggs and Fred Smith",
        "tags": "tas live searching for certainty"
    }, {
        "file": "2022-01-20 SFC.mp3",
        "series": "Tassie Encounters",
        "content": "Searching for Certainty: Healthy Choices",
        "guests": "Joe Bloggs and Fred Smith",
        "tags": "tas live searching for certainty"
    }, {
        "file": "wdyt-XYZZ-06-03 FULL.mp3",
        "series": "What Do You Think",
        "content": "Friendship Under Fire",
        "guests": "Mick Frederick",
        "tags": "whatdoyouthink frederick"
    }
];
$filtered = $smartSearch->filterArray($items));

# Example for Laravel Collection-based filtering:
$cItems = collect($items);
$cFiltered = $smartSearch->filterCollection($cItems));
```

## Laravel Models

Laravel models can be made made "Smart Searchable" by adding the *SmartSearchable* trait:

```php
# In your model...

use Miking7\SmartSearch\SmartSearchable;

class MyModel extends Model
{
    use SmartSearchable;
    ...
}
```

Your model can now be searched like this:

```php
MyModel()::smartSearch('joe', 'location|type')->get();
# OR
MyModel()::smartSearch('joe', ['location', 'type'])->get();
```

Extra attributes can be defined to provide defaults, and to allow greater control:

```php
class MyModel extends Model
{
    use SmartSearchable;

    // DEFAULT attributes to be searched
    protected $smartSearchableInclude = ['location', 'type'];

    // ALLOWED attributes to be searched
    protected $smartSearchableAllow =   ['asset_id', 'location', 'type'];

    ...
}
```

Your model can now be searched like this:

```php
MyModel()::smartSearch('joe')->get();
```

## Laravel Valet Resources

The default search functionality for Laravel Nova resources can be augmented with SmartSearch capabilities by simply adding the `SmartSearchableNovaResource` trait to your resource:

```php
# In your Nova Resource...

use Miking7\SmartSearch\SmartSearchableNovaResource;

class MyResource extends Resource
{
    use SmartSearchableNovaResource;
    ...
}
```

The only requirement is that `SmartSearchable` trait has been added to the underlying Model.

This trait automatically uses the Nova Resource's `$search` attribute column definitions, however these can 
be ignored in favour of those defined by the underlying model's `$smartSearchableInclude` attribute by setting:

```php
    protected static $smartSearchableIgnoreNovalSearchColumns = true;
```
