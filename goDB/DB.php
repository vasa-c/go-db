<?php
/**
 * goDB2: the library for working with relational databases (for PHP)
 *
 * @package go\DB
 * @license https://raw.github.com/vasa-c/go-db/master/LICENSE MIT
 * @link https://github.com/vasa-c/go-db source
 * @link https://github.com/vasa-c/go-db/wiki documentation
 * @uses PHP >= 5.3
 * @uses specific dependencies for a specific adapters
 */

namespace go\DB;

use go\DB\Helpers\Fetchers\Cursor as CursorFetcher;
use go\DB\Helpers\Connector;
use go\DB\Helpers\Templater;
use go\DB\Helpers\Config;
use go\DB\Exceptions\UnknownAdapter;
use go\DB\Exceptions\Query;
use go\DB\Exceptions\Closed;
use go\DB\Exceptions\ConfigSys;
use go\DB\Helpers\Debuggers\OutConsole as DebuggerOutConsole;
use go\DB\Helpers\Debuggers\OutHtml as DebuggerOutHtml;

/**
 * The basic class of database adapters
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
abstract class DB
{
    /**
     * Creates an instance for database access
     *
     * @param array $params
     *        database connection parameters
     * @param string $adapter [optional]
     *        a name of a db-adapter (if it not specified in $params)
     * @return \go\DB\DB
     *         the instance for database access
     * @throws \go\DB\Exceptions\Config
     *         parameters is invalid
     * @throws \go\DB\Exceptions\Connect
     *         a connection error
     */
    final public static function create(array $params, $adapter = null)
    {
        $adapter = isset($params['_adapter']) ? $params['_adapter'] : $adapter;
        $adapter = \strtolower($adapter);
        $classname = __NAMESPACE__.'\\Adapters\\'.\ucfirst($adapter);
        if (!\class_exists($classname, true)) {
            throw new UnknownAdapter($adapter);
        }
        $params['_adapter'] = $adapter;
        return (new $classname($params));
    }

    /**
     * Returns the list of available adapters
     *
     * @return array
     */
    final public static function getAvailableAdapters()
    {
        if (!self::$availableAdapters) {
            $adapters = array();
            foreach (\glob(__DIR__.'/Adapters/*.php') as $filename) {
                if (\preg_match('~([a-z0-9]*)\.php$~si', $filename, $matches)) {
                    $adapters[] = \strtolower($matches[1]);
                }
            }
            self::$availableAdapters = $adapters;
        }
        return self::$availableAdapters;
    }

    /**
     * Performs a query on the database
     *
     * @param string $pattern
     *        the query pattern
     * @param array $data [optional]
     *        the incoming data for the query pattern
     * @param string $fetch [optional]
     *        the result format
     * @param string $prefix [optional]
     *        a table prefix for the current query
     * @return \go\DB\Result
     *         the query result in specified format
     * @throws \go\DB\Exceptions\Connect
     *         an error of lazy connection
     * @throws \go\DB\Exceptions\Closed
     *         the connection is closed
     * @throws \go\DB\Exceptions\Templater
     *         an error of the templating system ($pattern or $data is invalid)
     * @throws \go\DB\Exceptions\Query
     *         an error of the query
     * @throws \go\DB\Exceptions\Fetch
     *         the result format is invalid for this query type
     */
    final public function query($pattern, $data = null, $fetch = null, $prefix = null)
    {
        $query = $this->makeQuery($pattern, $data, $prefix);
        return $this->plainQuery($query, $fetch);
    }

    /**
     * Performs a "plain" query
     *
     * @param string $query
     *        a plain query
     * @param string $fetch [optional]
     *        the result format
     * @return \go\DB\Result
     *         the query result in specified format
     * @throws \go\DB\Exceptions\Connect
     *         an error of lazy connection
     * @throws \go\DB\Exceptions\Closed
     *         the connection is closed
     * @throws \go\DB\Exceptions\Query
     *         an error of the query
     * @throws \go\DB\Exceptions\Fetch
     *         the result format is invalid for this query type
     */
    final public function plainQuery($query, $fetch = null)
    {
        $this->forcedConnect();
        $implementation = $this->connector->getImplementation();
        $connection = $this->connector->getConnection();
        $duration = \microtime(true);
        $cursor = $implementation->query($connection, $query);
        $duration = \microtime(true) - $duration;
        if (!$cursor) {
            $errorInfo = $implementation->getErrorInfo($connection);
            $errorCode = $implementation->getErrorCode($connection);
            throw new Query($query, $errorInfo, $errorCode);
        }
        $this->debugLog($query, $duration, null);
        $fetcher = $this->createFetcher($cursor);
        if ($fetch === null) {
            return $fetcher;
        }
        return $fetcher->fetch($fetch);
    }

    /**
     * @alias for query()
     *
     * The next examples are identical:
     * @example $db->query('SELECT * FROM `table`');
     * @example $db('SELECT * FROM `table`');
     *
     * @param string $pattern
     * @param array $data [optional]
     * @param string $fetch [optional]
     * @param string $prefix [optional]
     * @return \go\DB\Result
     * @throws \go\DB\Exceptions\Connect
     * @throws \go\DB\Exceptions\Closed
     * @throws \go\DB\Exceptions\Templater
     * @throws \go\DB\Exceptions\Query
     * @throws \go\DB\Exceptions\Fetch
     */
    final public function __invoke($pattern, $data = null, $fetch = null, $prefix = null)
    {
        return $this->query($pattern, $data, $fetch, $prefix);
    }

    /**
     * Checks if connection is actually established
     *
     * @return bool
     */
    final public function isConnected()
    {
        if ($this->hardClosed) {
            return false;
        }
        return $this->connector->isConnected();
    }

    /**
     * Chick if connection is closed (hard)
     *
     * @return bool
     */
    final public function isClosed()
    {
        return $this->hardClosed;
    }

    /**
     * Forced establishes a connection (if its is not establishes)
     *
     * @return bool
     *         a connection has been established at this time
     * @throws \go\DB\Exceptions\Connect
     *         a connection error
     * @throws \go\DB\Exceptions\Closed
     *         a connection has been closed (hard)
     */
    final public function forcedConnect()
    {
        if ($this->hardClosed) {
            throw new Closed();
        }
        if ($this->connected) {
            return false;
        }
        $res = $this->connector->connect();
        $this->connected = true;
        return $res;
    }

    /**
     * Closes a connection
     *
     * @param boolean $soft [optional]
     *        uses "soft" closing (it is possible to restore)
     * @return boolean
     *         a connection has been closed at this time
     */
    final public function close($soft = false)
    {
        if ($this->hardClosed) {
            return false;
        }
        $this->hardClosed = !$soft;
        if (!$this->connected) {
            return false;
        }
        $this->connected = false;
        return $this->connector->close();
    }

    /**
     * Sets the prefix for tables
     *
     * @param string $prefix
     */
    final public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Returns the prefix for tables
     *
     * @return string
     */
    final public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Sets a handler for the debug info
     *
     * @param callable $callback
     *        a callback($query, $duration, $info) or TRUE for the standard handler or NULL for disable
     */
    final public function setDebug($callback = true)
    {
        if ($callback === true) {
            if (\php_sapi_name() === 'cli') {
                $callback = new DebuggerOutConsole();
            } else {
                $callback = new DebuggerOutHtml();
            }
        }
        $this->debugCallback = $callback;
    }

    /**
     * Returns a debug handler
     *
     * @return callback
     */
    final public function getDebug()
    {
        return $this->debugCallback;
    }

    /**
     * Disables the debug info output
     */
    final public function disableDebug()
    {
        $this->debugCallback = null;
    }

    /**
     * Returns a low-level connection implementation (adapter depended)
     *
     * @param bool $connect
     *        force connect
     * @return mixed
     *         an implementation or FALSE if it is not created
     * @throws \go\DB\Exceptions\Connect
     * @throws \go\DB\Exceptions\Closed
     */
    final public function getImplementationConnection($connect = true)
    {
        if ($connect && (!$this->connector->isConnected())) {
            $this->forcedConnect();
        }
        return $this->connector->getConnection();
    }

    /**
     * Creates a query by a pattern and an incoming data
     *
     * @param string $pattern
     *        a query pattern
     * @param array $data
     *        an incoming data for the pattern
     * @param string $prefix
     *        a prefix for tables
     * @return string
     *         the plain query
     * @throws \go\DB\Exceptions\Templater
     *         an error of templating system
     */
    public function makeQuery($pattern, $data, $prefix = null)
    {
        Compat::setCurrentOpts($this->paramsSys['compat']);
        $this->forcedConnect();
        if ($prefix === null) {
            $prefix = $this->prefix;
        }
        $templater = $this->createTemplater($pattern, $data, $prefix);
        $templater->parse();
        return $templater->getQuery();
    }

    /**
     * Returns an object for a table access
     *
     * @param string $tablename
     *        a table name
     * @param array $map [optional]
     *        a map (a key => a real table column)
     * @return \go\DB\Table
     *         a "table" instance
     */
    public function getTable($tablename, array $map = null)
    {
        return new Table($this, $tablename, $map);
    }

    /**
     * The hidden constructor (for instance create use a static method create())
     *
     * @param array $params
     *        a database configuration
     * @thorws go\DB\Exceptions\Connect
     * @throws go\DB\Exceptions\ConfigConnect
     */
    protected function __construct($params)
    {
        $this->separateParams($params);
        $this->connector = $this->createConnector();
        if (!$this->paramsSys['lazy']) {
            $this->connector->connect();
            $this->connected = true;
        }
        $this->setPrefix($this->paramsSys['prefix']);
        $this->setDebug($this->paramsSys['debug']);
    }

    /**
     * The destructor
     */
    final public function __destruct()
    {
        $this->connector->close();
        $this->connector->removeLink();
        $this->connector = null;
    }

    /**
     * Cloning the instance
     */
    public function __clone()
    {
        $this->connector->addLink($this->connected);
    }

    /**
     * Creates an instance for a database connect
     *
     * @return \go\DB\Helpers\Connector
     */
    protected function createConnector()
    {
        return (new Connector($this->paramsSys['adapter'], $this->paramsDB));
    }

    /**
     * Creates a templating for a query
     *
     * @param string $pattern
     * @param array $data
     * @param string $prefix
     * @return \go\DB\Helpers\Templater
     */
    protected function createTemplater($pattern, $data, $prefix)
    {
        return (new Templater($this->connector, $pattern, $data, $prefix));
    }

    /**
     * Creates an instance for a result representation
     *
     * @param mixed $cursor
     * @return \go\DB\Result
     */
    protected function createFetcher($cursor)
    {
        return (new CursorFetcher($this->connector, $cursor));
    }

    /**
     * Analysis parameters and separates its to system and adapter-depending
     *
     * @param array $params
     * @throws \go\DB\Exceptions\ConfigSys
     */
    protected function separateParams($params)
    {
        $this->paramsDB = array();
        $this->paramsSys = Config::get('configsys');
        foreach ($params as $name => $value) {
            if (\substr($name, 0, 1) === '_') {
                $name = \substr($name, 1);
                if (!\array_key_exists($name, $this->paramsSys)) {
                    throw new ConfigSys('Unknown system param "'.$name.'"');
                }
                $this->paramsSys[$name] = $value;
            } else {
                $this->paramsDB[$name] = $value;
            }
        }
        return true;
    }

    /**
     * Sends a debug info to the handler
     *
     * @param string $query
     * @param float $duration
     * @param mixed $info
     */
    protected function debugLog($query, $duration, $info)
    {
        if ($this->debugCallback) {
            \call_user_func($this->debugCallback, $query, $duration, $info);
        }
        return true;
    }

    /**
     * The list of avaliable adapters (cache)
     *
     * @var array
     */
    private static $availableAdapters;

    /**
     * The connection instance
     *
     * @var \go\DB\Helpers\Connector
     */
    protected $connector;

    /**
     * System parameters
     *
     * @var array
     */
    protected $paramsSys;

    /**
     * Connection parameters
     *
     * @var array
     */
    protected $paramsDB;

    /**
     * The current table prefix
     *
     * @var string
     */
    protected $prefix;

    /**
     * The debug handler
     *
     * @var callback
     */
    protected $debugCallback;

    /**
     * Flag: the connection is established
     *
     * @var bool
     */
    protected $connected = false;

    /**
     * Flag: the connection is closed (hard)
     *
     * @var bool
     */
    protected $hardClosed = false;
}

/**
 * @alias \go\DB\DB::create
 *
 * @param array $params
 * @param string $adapter [optional]
 * @return \go\DB\DB
 * @throws \go\DB\Exceptions\Config
 * @throws \go\DB\Exceptions\Connect
 */
function create(array $params, $adapter = null)
{
    return DB::create($params, $adapter);
}

/**
 * @alias \go\DB\Storage::query
 *
 * @param string $pattern
 * @param array $data [optional]
 * @param string $fetch [optional]
 * @param string $prefix [optional]
 * @throws \go\DB\Exceptions\StorageDBCentral
 * @throws \go\DB\Exceptions\Connect
 * @throws \go\DB\Exceptions\Closed
 * @throws \go\DB\Exceptions\Templater
 * @throws \go\DB\Exceptions\Query
 * @throws \go\DB\Exceptions\Fetch
 */
function query($pattern, $data = null, $fetch = null, $prefix = null)
{
    return Storage::getInstance()->query($pattern, $data, $fetch, $prefix);
}
