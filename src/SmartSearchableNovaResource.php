<?php

namespace FaithFM\SmartSearch;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
    protected static $smartSearchableIgnoreNovaSearchColumns = false;

    /**
     * Override default Nova search with our SmartSearch library
     */
    protected static function initializeSearch(Builder $query, string $search, array $searchColumns): Builder
    {
        // Ensure that any instances of Nova's special "Columns" instance are converted back to field names (string) )
        foreach ($searchColumns as &$searchColumn) {
            if ($searchColumn instanceof Column)
                $searchColumn = $searchColumn->column;
        }

        if ($query->hasNamedScope('smartSearch')) {
            // Use "smartSearch" scope of underlying model (if it exists)
            if (!static::$smartSearchableIgnoreNovaSearchColumns) {
                // By default, the search is performed using Nova's $searchColumns
                return $query->smartSearch($search, $searchColumns);

            } else {
                // Otherwise, search using columns defined by the underlying Model's $smartSearchableInclude attribute
                return $query->smartSearch($search);
            }

        } else {
            // Use our own "smartSearch" instance (if "smartSearch" scope does not exist on underlying model)
            $smartSearch = new SmartSearch($search, $searchColumns);
            return $query->where($smartSearch->getBuilderFilter());
        }

    }

}
