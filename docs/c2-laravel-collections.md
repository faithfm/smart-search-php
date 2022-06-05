# Laravel Collection filtering:

The `filterCollection()` method filters a Laravel Collection:

```php 
use FaithFM\SmartSearch\SmartSearch;

$search = 'optus location:stock -F2701';
$smartSearch = new SmartSearch($search, 'asset_id|location|identifier');

$colItems = collect([
    {
        "id": 1624,
        "asset_id": "F2699",
        "location": "Holton Stock",
        "identifier": "Optus190"
    }, {
        "id": 1623,
        "asset_id": "F2701",
        "location": "Melbourne",
        "identifier": "Telstra156"
    }, {
        "id": 1618,
        "asset_id": "F2693",
        "location": "Holton Stock",
        "identifier": "Optus184"
    }
]);

# Apply the Collection filter
$cFiltered = $smartSearch->filterCollection($colItems));
```

`$cFiltered` result contains the 1st + 3rd Collection items.
