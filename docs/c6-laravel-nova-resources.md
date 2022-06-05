# Laravel Nova Resource Filtering:

The default search functionality for Laravel Nova resources can be augmented with SmartSearch capabilities by simply adding the `SmartSearchableNovaResource` trait to your resource:

```php
# In your Nova Resource...

use FaithFM\SmartSearch\SmartSearchableNovaResource;

class MyResource extends Resource
{
    use SmartSearchableNovaResource;
    ...
}
```

This will override the default Nova search engine and allow you to perform SmartSearch queries directly on your Nova Resource:
![smart-search-01.jpg](smart-search-01.jpg)

> Note: the `SmartSearchableNovaResource` trait will work regardless of whether or not the `SmartSearchable` trait has been added to the underlying Model.  
> If it has the relevant `smartSearch` Model scope will be detected and used, otherwise a SmartSeach operation will be performed directly at the Resource level.

## Default Search Columns:

By default this trait automatically uses the Nova Resource's `$search` attribute column definitions, however these can be ignored in favour of those defined by the underlying model's `$smartSearchableInclude` attribute:

  1. The `SmartSearchable` trait has been added to the underlying Model.
   
  2. The underlying Model has defined a list of default search fields using its `$smartSearchableInclude` attribute.

  3. The Nova Resource sets the following attribute = **true**.
```php
protected static $smartSearchableIgnoreNovaSearchColumns = true;
```
