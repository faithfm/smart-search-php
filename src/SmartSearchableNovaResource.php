<?php

namespace Miking7\SmartSearch;

/**
 * This trait allows a Laravel Nova Resource to be SmartSearchable
 * 
 * Note: the SmartSearchable trait must also be added to the relavent Model
 */
trait SmartSearchableNovaResource
{
    /**
     * Ignore the Nova Resource's "$search" attribute column definitions, in favour of using 
     * those defined in underlying model's $smartSearchableInclude attribute.
     *
     * @var array
     */
    protected static $smartSearchableIgnoreNovalSearchColumns = false;

    /**
     * Override default Nova search with our SmartSearch library
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @param  array  $searchColumns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function initializeSearch($query, $search, $searchColumns)
    {
        if (static::$smartSearchableIgnoreNovalSearchColumns)
            return $query->smartSearch($search);
        else
            return $query->smartSearch($search, $searchColumns);
    }

}
