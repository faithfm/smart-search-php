# SQL Database where-clause filtering:

The `getSqlFilter()` method generates an SQL "where clause" that can be used to filter your SQL statement.

Before this method can be called, the relevant "escape function" for your database driver must be provided.  (Used to prevent SQL injection attacks).  The example below assumes we are using PDO.

```php
use FaithFM\SmartSearch\SmartSearch;

$search = 'optus 320 location:stock -F2701';
$smartSearch = new SmartSearch($search, 'asset_id|location|type|make|model|identifier');

// Provide escape function
$smartSearch->setSqlEscapeStringFn(function($str) use ($myPdoConnection) {
    return $myPdoConnection->quote($str);
});

// Now generate the where-clause (and use it in our SQL statement):
$whereClause = $smartSearch->getSqlFilter());
$sql = “SELECT * FROM content WHERE ... AND “. $whereClause;
```

## Alternative Wildcard Operators

The `*` and `?` wildcard operators are used in our search syntax, but database engines often utilise different wildcard operators in their `LIKE` queries.

The `%` and `_` wildcard operators (used by most SQL languages) are assumed by default, but other operators can be specified if required:

```php
$options = [
    'sqlWildcard' => '*',
    'sqlWildcardSingleChar' => '?',
];
$smartSearch = new SmartSearch($search, 'asset_id|location|type|make|model|identifier', null, $options);
```
