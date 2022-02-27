<?php

namespace App\Libraries;

use stdClass;
use Closure;
use Exception;

class SmartSearchFilter
{
    /**
     * The search pattern
     *
     * @var string
     */
    public $searchString;

    /**
     * The parsed search - as a nested array of filter operations
     *
     * @var array
     */
    public $filterOpsArray;

    /**
     * Any errors encountered during parsing
     *
     * @var string
     */
    public $errors;

    /**
     * List of fields to be search by default (when no fields are specified in the search string)
     *
     * @var array
     */
    public $defaultFields;

    /**
     * List of allowed search fields.  (If null, $defaultFields is used instead)
     *
     * @var array
     */
    public $allowedFields;

    /**
     * Filter options
     *
     * @var stdClass
     */
    protected $options;

    /**
     * Default filter options
     *
     * @var array
     */
    const DEFAULT_OPTIONS = [
        'caseSensitive' => false,
        'sqlWildcard' => '%',
        'sqlWildcardSingleChar' => '_',
    ];

    /**
     * Function/closure to be used to escape strings so it is safe to use in an SQL query (ie: protect against SQL injection attacks)
     *
     * @var Closure
     */
    protected $sqlEscapeStringFn;


    /**
     * Create SmartSearchFilter
     *
     * @param  mixed         $defaultFields
     * @param  mixed         $allowedFields     (empty-string value implies $allowedFields=$defaultFields)
     * @param array|stdClass $options
     * @param Closure $sqlEscapeStringFn
     * @return void
     */
    public function __construct($defaultFields = "", $allowedFields = "", $options = [], Closure $sqlEscapeStringFn = null)
    {
        $this->defaultFields = $this->getArrayableItems($defaultFields);
        $this->allowedFields = $this->getArrayableItems($allowedFields);
        if ($this->allowedFields == [])
            $this->allowedFields = $this->defaultFields;

        // allow structure to be specified as either array or stdClass
        if ($options instanceof stdClass)
            $options = (array) $options;

        // convert default structure array values to a stdClass object
        $this->options = (object) array_merge(self::DEFAULT_OPTIONS, $options);

        // Assign sqlEscapeStringFn
        if ($sqlEscapeStringFn)
            $this->sqlEscapeStringFn = $sqlEscapeStringFn;
        else
            // Default closure raises an error if called
            $this->sqlEscapeStringFn = function($s) {
                throw new Exception('ERROR: sqlEscapeString function not specified - suggest using: "$pdo->quote", "mysqli_real_escape_string", "esc_sql", etc');
            };
    }


    /**
     * Return array of items from array, array-castable, comma/pipe-separated list of strings, or even a null.
     *
     * Similiar to Laravel's getArrayableItems().  Doesn't handle Laravel arrayable types, but does handle string lists)
     *
     * @param  mixed  $values
     * @return array
     */
    protected function getArrayableItems($values)
    {
        // null
        if (is_null($values))
            return [];

        // array
        if (is_array($values))
            return $values;

        // comma-separated or pipe-separated list
        if (is_string($values))
            return array_filter(array_map('trim', explode('|', str_replace(',', '|', $values))));

        // everything else: try to type-cast
        return (array) $values;
    }


    /**
     * Parse the specified search string (into a filterOpsArray for later use)
     *
     * @var string $searchString
     * @return void
     */
    public function parse($searchString = '') {

        $this->searchString = $searchString ?? '';
        $this->filterOpsArray = [];
        $this->errors = [];

        // split search string into phrases - taking quotation marks into account - ref: https://stackoverflow.com/questions/2817646/javascript-split-string-on-space-or-on-quotes-to-array
        $search_count = preg_match_all('/-{0,1}([a-z0-9|,_]+:){0,1}"[^"]+"|[^ ]+|\|/i', $this->searchString, $search_phrases);
        if ($search_count > 0)
            $search_phrases = $search_phrases[0];
        else
            $search_phrases = [];

        // split phrases into "AND" groups (between "|" / "OR" operators)
        // ie: searchString: 'cristian week | rolf taree'  -->  means: OR( AND(cristian,week), AND(rolf,taree) )  -->  and_groups_phrases = [ ['cristian','week'], ['rolf','taree'] ]
        $and_groups_phrases = [[]];
        foreach ($search_phrases as $phrase) {
            if ($phrase == "|")
                $and_groups_phrases[] = [];  // start new group
            else
                $and_groups_phrases[count($and_groups_phrases)-1][] = $phrase;
        }

        // decode phrase arrays into filter operations arrays
        $and_groups_filter_ops = array_map(function($and_phrases) {
            $and_array = array_map(function($fullPhrase) {
                $phrase = $fullPhrase;
                $invert = false;
                if ($phrase[0] == '-') {            // starts-with '-'
                    $invert = true;
                    $phrase = substr($phrase, 1);   // remove leading '-'
                };
                $searchFields = $this->defaultFields;   // default
                $match_count = preg_match_all('/([a-z0-9|,_]+):/i', $phrase, $match);
                if ($match_count > 0) {
                    $searchFields = $match[1][0];
                    $phrase = substr($phrase, strlen($searchFields)+1);
                    if (!is_array($searchFields)) {
                        $searchFields = explode('|', str_replace(',', '|', $searchFields));    // split fields by '|' or ','
                        $searchFields = $this->validateSearchFields($searchFields);
                        if ($searchFields == []) {
                            $this->addError("Search phrase '$fullPhrase' contains no valid search fields - skipping.");
                            return [];
                        }
                    }

                }
                $phrase = str_replace('"', '', $phrase);    // remove quotation marks
                $phrase = str_replace('|', '', $phrase);    // remove "|" occurances (usually only if errors in syntax)
                if ($phrase == "") {
                    $this->addError("Search phrase '$fullPhrase' contains empty search term - skipping.");
                    return [];
                }
                $main_filter_ops = ["MATCH", $searchFields, $phrase];
                if ($invert)
                    $main_filter_ops = ["NOT", $main_filter_ops];    // invert if required

                return $main_filter_ops;

            }, $and_phrases);

            $and_array = array_filter($and_array, fn($filter_op) => $filter_op<>[] );   // remove blank search terms
            if ($and_array <> [])
                return ["AND", ...$and_array];
            else
                return [];

        }, $and_groups_phrases);

        $and_groups_filter_ops = array_filter($and_groups_filter_ops, fn($filter_op) => $filter_op<>[] );   // remove blank search terms
        $this->filterOpsArray = ["OR", ...$and_groups_filter_ops];
    }


    /**
     * Filter parsed search fields - to ensure that only 'allowed' fields can be searched
     *
     * @var array $searchFields
     * @return array
     */
    protected function validateSearchFields($searchFields) {
        return array_filter($searchFields, function($field) {
            if (in_array($field, $this->allowedFields))
                return true;
            else {
                $this->addError("Field '$field' not found in list of allowed fields (". join(", ", $this->allowedFields) .")");
                return false;
            }
        });
    }


    /**
     * Record an error
     *
     * @var string $msg
     * @return  void
     */
    protected function addError(string $msg) {
        $this->errors[] = $msg;
    }


    /**
     * Return description (visual hierarchical) of filterOpsArray
     *
     * @var string|null $filter_operations_array    (not normally specified except when called recursively)
     * @return string
     */
    public function getFilterOpsDescription($filter_operations_array = null) {
        # This function is called recursively.  Start with $this->filterOpsArray if null
        if (is_null($filter_operations_array))
            $filter_operations_array = $this->filterOpsArray;

        $fn = array_shift($filter_operations_array);
        $params = $filter_operations_array;
        $fns = [
            'AND' => function($params) {
                $paramsDescriptions = array_map(
                    fn($param) => $this->getFilterOpsDescription($param),
                    $params
                );
                $indented = static::indent(join(",\n", $paramsDescriptions));
                return "AND(\n$indented )";
            },
            'OR' => function($params) {
                $paramsDescriptions = array_map(
                    fn($param) => $this->getFilterOpsDescription($param),
                    $params
                );
                $indented = static::indent(join(",\n", $paramsDescriptions));
                return "OR(\n$indented )";
            },
            'NOT' => function($params) {
                $param = $params[0];
                $unindented = $this->getFilterOpsDescription($param);
                return "NOT( $unindented )";
            },
            'MATCH' => function($params) {
                [$fields, $glob] = $params;
                $joined = join('|', $fields);
                return "MATCH(\"$joined\", \"$glob\")";
            },
            'default' => function() {
                die("INVALID CODE");
            }
        ];
        return ($fns[$fn] ?? $fns['default'])($params);  // call the appropriate mapped function
    }


    /**
     * Return SQL where-clause filter from filterOpsArray
     *
     * @var string|null $filter_operations_array    (not normally specified except when called recursively)
     * @return string
     */
    public function getSqlFilter($filter_operations_array = null) {
        # This function is called recursively.  Start with $this->filterOpsArray if null
        if (is_null($filter_operations_array))
            $filter_operations_array = $this->filterOpsArray;

        $sqlEscapeStringFn = $this->sqlEscapeStringFn;  // local reference
        $fn = array_shift($filter_operations_array);
        $params = $filter_operations_array;
        $fns = [
            'AND' => function($params) {
                $paramsDescriptions = array_map(
                    fn($param) => static::indent($this->getSqlFilter($param)),
                    $params
                );
                $indented = join("\n) AND (\n", $paramsDescriptions);
                return "(\n$indented\n)";
            },
            'OR' => function($params) {
                $paramsDescriptions = array_map(
                    fn($param) => static::indent($this->getSqlFilter($param)),
                    $params
                );
                $indented = join("\n) OR (\n", $paramsDescriptions);
                return "\n(\n$indented\n)";
            },
            'NOT' => function($params) {
                $param = $params[0];
                $unindented = $this->getSqlFilter($param);
                return "NOT( $unindented )";
            },
            'MATCH' => function($params) use ($sqlEscapeStringFn) {
                [$fields, $glob] = $params;

                // if no wildcards in search term, assume we're searching for the term anywhere in the field
                if (!static::strContains($glob, ['*', '?']))
                    $glob = "*$glob*";

                // replace generic wildcards (ie: */?) with sql-specific ones (ie: %/_)
                $glob = str_replace('*', $this->options->sqlWildcard,           $glob);
                $glob = str_replace('?', $this->options->sqlWildcardSingleChar, $glob);

                // make glob SQL-safe (ie: prevent SQL injection attacks)
                $safeGlob = $sqlEscapeStringFn($glob);

                $fstrings = array_map(
                    fn($field) => "($field like $safeGlob)",
                    $fields
                );
                return join(' OR ', $fstrings);
            },
            'default' => function() {
                die("INVALID CODE");
            }
        ];
        return ($fns[$fn] ?? $fns['default'])($params);  // call the appropriate mapped function
    }


    /**
     * Split text into lines and add 2x spaces to the beginning of each line
     *
     * @var string $text
     * @return string
     */
    protected static function indent($text) {
        $lines = explode("\n", $text);
        $indented_lines = array_map(
            fn($line) => "  $line",
            $lines
        );
        return join("\n", $indented_lines);
    }


    // *** protected clone of basic (Laravel Illuminate\Support\Str) helper function(s) - to allow for a dependency-free class ***
    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|string[]  $needles
     * @return bool
     */
    protected static function strContains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

}
