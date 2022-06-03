<?php

namespace FaithFM\SmartSearch;

use FaithFM\SmartSearch\SmartSearch;

/**
 * This trait allows a Laravel Model to be SmartSearchable
 * 
 * Example: MyModel()::smartSearch('joe')->get();
 * 
 * Note: The $includeAttribs parameter must be specified when calling the scope 
 *   unless the $smartSearchableInclude attribute has been defined for the model.
 */
trait SmartSearchable
{
    /**
     * The attributes that are INCLUDED in smart-searchable queries by default
     *
     * @var array
     */
    protected $smartSearchableInclude = [
        // 'asset_id', 'location', 'type'
    ];

    /**
     * The attributes that are ALLOWED in smart-searchable queries
     *
     * Note: same as $smartSearchableInclude if not specified
     *
     * @var array
     */
    protected $smartSearchableAllow = [
    ];

    /**
     * Scope a query using a SmartSearch
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSmartSearch($query, $search, $includeAttribs = null, $allowedAttribs = null, $options = [])
    {
        $includeAttribs = $includeAttribs ?? $this->smartSearchableInclude;
        $allowedAttribs = $allowedAttribs ?? $this->smartSearchableAllow;
        $smartSearch = new SmartSearch($includeAttribs, $allowedAttribs, $options);
        $smartSearch->parse($search);
        return $query->where($smartSearch->getBuilderFilter());
    }

}
