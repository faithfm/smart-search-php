# Laravel Database Query Builder filtering:

The `getBuilderFilter()` method generates a Larael Database Query Builder that can be applied to a Laravel DB (or Model):

```php
use FaithFM\SmartSearch\SmartSearch;

$search = 'optus 320 location:stock -F2701';
$smartSearch = new SmartSearch($search, 'asset_id|location|type|make|model|identifier');

// Apply the filter to a Laravel DB table
$data = DB::table('my_table')::where( $smartSearch->getBuilderFilter() )->get();

// Apply the filter to a Laravel Model
$data = MyModel::where( $smartSearch->getBuilderFilter() )->get();
```

> Note: Don't forget to install the [additional "whereNot" query dependency](docs/wherenot-dependency.md) if you are using Laravel <= 8.x.  (Not required for Laravel >= 9.x)
