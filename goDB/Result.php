<?php
/**
 * @package go\DB
 */

namespace go\DB;

/**
 * The result representation interface
 *
 * @example $result = $db->query($pattern, $data);
 * The $result object encapsulates the specific implementation of the cursor.
 * For mysqli-adapter (for example) it is a mysqli_result instance.
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
interface Result extends \IteratorAggregate, \Countable
{
    /**
     * Represents result as specified in $fetch
     *
     * @param string $fetch
     *        a representation format
     * @return mixed
     *         the result in specified format
     * @throws \go\DB\Exceptions\Fetch
     *         the format is invalid for this result type
     */
    public function fetch($fetch);

    /**
     * Clears the result
     */
    public function free();

    /**
     * As a list of dictionaries
     *
     * @param string $param [optional]
     *        a column for keys of the main list (numeric list by default)
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function assoc($param = null);

    /**
     * As a list of numerics array
     *
     * @param int $param [optional]
     *        a column number for keys of the main list (numeric list by default)
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function numerics($param = null);

    /**
     * As a list of objects
     *
     * @param string $param [optional]
     *        a column for keys of the main list (numeric list by default)
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function objects($param = null);

    /**
     * As a list of scalar values (a single column)
     *
     * @param mixed $param [optional]
     *        a used column (first by default)
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function col($param = null);

    /**
     * As a variables list (a first row in result is a key, a second is a value)
     *
     * @param mixed $param [optional]
     *        does not used
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function vars($param = null);

    /**
     * An iterator, analog for assoc()
     *
     * @param mixed $param [optional]
     * @return \Traversable
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function iassoc($param = null);

    /**
     * An iterator, analog for numerics()
     *
     * @param mixed $param [optional]
     * @return \Traversable
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function inumerics($param = null);

    /**
     * An iterator, analog for objects()
     *
     * @param mixed $param [optional]
     * @return \Traversable
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function iobjects($param = null);

    /**
     * An iterator, analog for vars()
     *
     * @param mixed $param [optional]
     * @return \Traversable
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function ivars($param = null);

    /**
     * An iterator, analog for col()
     *
     * @param mixed $param [optional]
     * @return \Traversable
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function icol($param = null);

    /**
     * A dictionary of a single row fields (NULL for empty result)
     *
     * @param mixed $param [optional]
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function row($param = null);

    /**
     * A numeric list of a single row fields (NULL for empty result)
     *
     * @param mixed $param [optional]
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function numeric($param = null);

    /**
     * A object as a dictionary a single row fields (NULL for empty result)
     *
     * @param mixed $param [optional]
     * @return object
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function object($param = null);

    /**
     * A single value (a first column of a first row, NULL for empty result)
     *
     * @param mixed $param [optional]
     * @return string
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function el($param = null);

    /**
     * A single value as boolean (a first column of a first row, NULL for empty result)
     *
     * @param mixed $param [optional]
     * @return bool
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function bool($param = null);

    /**
     * Number of rows in the result
     *
     * @param mixed $param [optional]
     * @return int
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function num($param = null);

    /**
     * The last auto increment
     *
     * @param mixed $param [optional]
     * @return int
     */
    public function id($param = null);

    /**
     * Number of affected rows
     *
     * @param mixed $param [optional]
     * @return int
     */
    public function ar($param = null);

    /**
     * The low-level implementation of cursor (adapter depended)
     *
     * @param mixed $param [optional]
     * @return mixed
     */
    public function cursor($param = null);
}
