# Changelog

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

No unreleased changes

## 1.2.0 - 2022-05-17

### Added filtering for arrays and Collections

* filterArray()...      to filter an array of items.
* filterCollection()... to filter a Laravel Collection.
* testItem()...         to test a single item.

## 1.1.0 - 2022-05-17

### Added database filtering via Laravel Query Builder

* getBuilderFilter()... to support filtering with Laravel Query Builder.

## 1.0.0 - 2022-02-27

### Initial library with filtering via SQL where-clause generation

* Created repo as new composer package from standalone class source file.
* getSqlFilter()... is the initial filtering option for creating SQL where-clauses
