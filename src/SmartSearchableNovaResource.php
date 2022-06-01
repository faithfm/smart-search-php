<?php

namespace Miking7\SmartSearch;
use Laravel\Nova\Query\Search\Column;

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
        if (static::$smartSearchableIgnoreNovalSearchColumns) {
            // search using underlying model's $smartSearchableInclude attribute
            return $query->smartSearch($search);
        } else {
            // First: convert any instances of Nova's special "Columns" instance back to field names (string)
            foreach ($searchColumns as &$searchColumn) {
                if ($searchColumn instanceof Column)
                    $searchColumn = $searchColumn->column;
            }
            // ...now search using the Nova Resource's $search attribute columns
            return $query->smartSearch($search, $searchColumns);
        }
    }

}
