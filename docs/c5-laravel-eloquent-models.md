# Context 5 - Laravel Eloquent Model filtering:

Laravel Eloquent models can be made made "Smart Searchable" by adding the *SmartSearchable* trait:

```php
# In your model...

use FaithFM\SmartSearch\SmartSearchable;

class MyModel extends Model
{
    use SmartSearchable;
    ...
}
```

Your model can now be searched using the `smartSearch` model scope:

```php
MyModel()::smartSearch('joe', 'location|type')->get();
```

The following extra Model attributes can be assigned for greater control:

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

Your model can now be searched more simply like this:

```php
MyModel()::smartSearch('joe')->get();
```
