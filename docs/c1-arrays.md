# Array filtering:

The `filterArray()` method filters an array of items:

```php 
use FaithFM\SmartSearch\SmartSearch;

$search = 'optus location:stock -F2701';
$smartSearch = new SmartSearch($search, 'asset_id|location|identifier');

$items = [
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
];

# Apply array filter
$filtered = $smartSearch->filterArray($items));
```

`$filtered` result contains the 1st + 3rd array items.

