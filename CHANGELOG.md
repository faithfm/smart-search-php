# Changelog

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

No unreleased changes

## 1.5.3 - 2022-05-31

### Bug-fix return-types for array+collection filter functions (in their DocBlocks)

## 1.5.2 - 2022-05-31

### Bug-fix regex replacing special characters (was broken)

## 1.5.1 - 2022-05-24

### Bug-fix missing "use"

## 1.5 - 2022-05-24

### Bug-fix to correctly handle Nova's special "Columns" instances (in list of searchable columns).

## 1.4.2 - 2022-05-23

### Corrected documentation typos

## 1.4.0 - 2022-05-20

### Added two Laravel traits

* `SmartSearchable` trait for Laravel Models
* `SmartSearchableNovaResource` trait for Laravel Nova Resources

## 1.3.0 - 2022-05-17

### Added basic usage example documentation

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
