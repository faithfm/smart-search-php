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
