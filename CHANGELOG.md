# Changelog

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 2.0.0 - 2022-06-05

* SmartSearch.php:
  * **BREAKING CHANGE** - Incorporate `$search` into constructor() params.
  * This avoids the need to call `parse()` separately.
  * Add `setSqlEscapeStringFn()` function to allow deferred assignment if escape function was not specified during construction.
  * Change the way Exceptions are raised when `$sqlEscapeStringFn` is missing
* Major rewrite of documentation.
* This composer library is now published on packagist (no need for VCS reference).
* Truncate the inherited CHANGELOG history.  (Early development)

## 1.6.0 - 2022-06-03

* Change "miking7" --> "faithfm" namespace

## 1.5.4 (and earlier) - 2022-06-01

* Clone from early dev repo miking7/smart-search-php (v1.5.4).  Squash history.
