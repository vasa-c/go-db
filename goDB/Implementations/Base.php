<?php
/**
 * @package    go\DB
 */

namespace go\DB\Implementations;

/**
 * The abstraction on a concrete low-level implementation database access
 *
 * For example, the MySQL-adapter is an abstraction under php_mysql.
 * To do this, the Base-class inherited and its child delegates calls to mysqli_* functions.
 *
 * The implementation instance don't contains individual properties.
 * Methods receives a connection and a cursor as arguments.
 *
 * All public method of this class is abstract (theoretically).
 * But some of its is already defined because many drivers have same behaviour.
 *
 * Methods returns FALSE in case of an error (not exception).
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
abstract class Base
{
    /**
     * The list of required connection parameters
     * (for override)
     *
     * @var array
     */
    protected $paramsReq = array();

    /**
     * The list of optional connection parameters
     * (for override)
     *
     * a parameter name => default value
     *
     * @var array
     */
    protected $paramsDefault = array();

    /**
     * Creates an instance for the specified adapter
     *
     * @param string $adapter
     * @return \go\DB\Implementations\Base
     */
    public static function getImplementationForAdapter($adapter)
    {
        if (!isset(self::$cacheAdapters[$adapter])) {
            $classname = __NAMESPACE__.'\\'.\ucfirst($adapter);
            self::$cacheAdapters[$adapter] = new $classname();
        }
        return self::$cacheAdapters[$adapter];
    }

    /**
     * The hidden constructor. Building only by getImplementationForAdapter()
     */
    protected function __construct()
    {
    }

    /**
     * Connects to a databse server and selects the specific database
     *
     * The error information cannot find by getErrorInfo (because a connection don't returns).
     * For this used arguments by references.
     *
     * @param array $params
     * @param string & $errroInfo
     * @param int & $errorCode
     * @return mixed
     *         an implementation of connection or FALSE if error
     */
    abstract public function connect(array $params, &$errorInfo = null, &$errorCode = null);

    /**
     * Closes the connection
     *
     * @param mixed $connection
     */
    abstract public function close($connection);

    /**
     * Validates and normalizes connection parameters
     *
     * @param array $params
     * @return array
     *         normalized parameters or FALSE if parameters is invalid
     */
    public function checkParams(array $params)
    {
        $result = array();
        foreach ($this->paramsReq as $param) {
            if (!\array_key_exists($param, $params)) {
                return false;
            }
            $result[$param] = $params[$param];
        }
        foreach ($this->paramsDefault as $param => $default) {
            if (\array_key_exists($param, $params)) {
                $result[$param] = $params[$param];
            } else {
                $result[$param] = $default;
            }
        }
        return $result;
    }

    /**
     * Checks if a query result is a cursor (SELECT)
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return bool
     */
    public function isCursor($connection, $result)
    {
        return ($result !== true);
    }

    /**
     * Performs a query to the database
     *
     * @param mixed $connection
     * @param string $query
     *        the sql query
     * @return mixed
     *         a cursor for SELECT, TRUE for other, FALSE for error
     */
    abstract public function query($connection, $query);

    /**
     * Returns a last autoincrement (or its analog)
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    abstract public function getInsertId($connection, $cursor = null);

    /**
     * Returns the number of rows who affected by the last query
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    abstract public function getAffectedRows($connection, $cursor = null);

    /**
     * Returns a last error description
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return string
     */
    abstract public function getErrorInfo($connection, $cursor = null);


    /**
     * Returns a last error code
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    abstract public function getErrorCode($connection, $cursor);


    /**
     * Returns the number of the result rows
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return int
     */
    abstract public function getNumRows($connection, $cursor);

    /**
     * Returns a next line as a numerics array
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    abstract public function fetchRow($connection, $cursor);

    /**
     * Returns a next line as an associative array
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    abstract public function fetchAssoc($connection, $cursor);

    /**
     * Returns a next line as a object
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return object|false
     */
    public function fetchObject($connection, $cursor)
    {
        $result = $this->fetchAssoc($connection, $cursor);
        return $result ? (object)$result : false;
    }

    /**
     * Frees the cursor
     *
     * @param mixed $connection
     * @param mixed $cursor
     */
    abstract public function freeCursor($connection, $cursor);

    /**
     * Escapes special symbols in a string
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function escapeString($connection, $value)
    {
        return \addslashes($value);
    }

    /**
     * Represents a string as a data
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function reprString($connection, $value)
    {
        return '"'.$this->escapeString($connection, $value).'"';
    }

    /**
     * Represents a integer number as a data
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function reprInt($connection, $value)
    {
        $value = (int)$value;
        if ($value < 0) {
            $value = '('.$value.')';
        }
        return $value;
    }

    /**
     * Represents a float number as a data
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function reprFloat($connection, $value)
    {
        return (0 + $value);
    }

    /**
     * Represents a boolean value as a data
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function reprBool($connection, $value)
    {
        return $value ? '1' : '0';
    }

    /**
     * Represents NULL as a data
     *
     * @param mixed $connection
     * @return string
     */
    public function reprNULL($connection)
    {
        return 'NULL';
    }

    /**
     * Represents a table
     *
     * @param mixed $connection
     * @param string $value
     * @return string
     */
    public function reprTable($connection, $value)
    {
        return $this->reprField($connection, $value);
    }

    /**
     * Represents a column
     *
     * @param mixed $connection
     * @param string $value
     * @return string
     */
    public function reprCol($connection, $value)
    {
        return $this->reprField($connection, $value);
    }

    /**
     * Represents a fields chain
     * For example: db.table.col
     *
     * @param mixed $connection
     * @param array $fields
     * @return string
     */
    public function reprChainFields($connection, array $fields)
    {
        $result = array();
        foreach ($fields as $field) {
            $result[] = $this->reprField($connection, $field);
        }
        return \implode('.', $result);
    }

    /**
     * Represents a field (a table or a column)
     *
     * @param mixed $connection
     * @param string $value
     * @return string
     */
    protected function reprField($connection, $value)
    {
        return '"'.$value.'"';
    }

    /**
     * Go to the start of the cursor
     *
     * @param mixed $connection
     * @param mixed $cursor
     */
    abstract public function rewindCursor($connection, $cursor);

    /**
     * The cache of instances (an adapter => an object)
     *
     * @var array
     */
    private static $cacheAdapters = array();
}
