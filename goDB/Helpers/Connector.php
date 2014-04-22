<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers;

use \go\DB\Implementations\Base as BaseImp;
use go\DB\Exceptions\ConfigConnect;
use go\DB\Exceptions\Connect;

/**
 * Abstract database connection
 *
 * Contains a low-level connect implementation.
 * Can be divided among several database objects.
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Connector
{
    /**
     * The constructor
     *
     * @param string $adapter
     *        the adapter name
     * @param array $params
     *        the connection parameters
     * @throws \go\DB\Exceptions\ConfigConnect
     *         the connection parameters is invalid
     */
    public function __construct($adapter, array $params)
    {
        $this->implementation = BaseImp::getImplementationForAdapter($adapter);
        $this->params = $this->implementation->checkParams($params);
        if (!$this->params) {
            throw new ConfigConnect();
        }
        $this->countLinks = 1;
        $this->countConnections = 0;
    }

    /**
     * The destructor
     */
    public function __destruct()
    {
        $this->deny();
    }

    /**
     * The connection requirement
     *
     * @return bool
     *         TRUE if connection has been established in this time
     * @throws \go\DB\Exceptions\Connect
     *         a connect error
     */
    public function connect()
    {
        if ($this->connection) {
            $this->countConnections++;
            return false;
        }
        $connection = $this->implementation->connect($this->params, $errorInfo, $errorCode);
        if (!$connection) {
            throw new Connect($errorInfo, $errorCode);
        }
        $this->connection = $connection;
        $this->countConnections = 1;
        return true;
    }

    /**
     * The closing requirement
     *
     * @return bool
     *         TRUE if connection has been closed in this time
     */
    public function close()
    {
        if (!$this->connection) {
            return false;
        }
        $this->countConnections--;
        if ($this->countConnections > 0) {
            return false;
        }
        $this->implementation->close($this->connection);
        $this->connection = null;
        return true;
    }

    /**
     * Checks if connection is established
     *
     * @return bool
     */
    public function isConnected()
    {
        return (!empty($this->connection));
    }

    /**
     * Appends a link to this connection
     *
     * @param bool $connection
     *        this database has connection already
     */
    public function addLink($connection)
    {
        $this->countLinks++;
        if ($connection) {
            $this->countConnections++;
        }
    }

    /**
     * Removes alink to this connection
     */
    public function removeLink()
    {
        if ($this->countLinks > 0) {
            $this->countLinks--;
            if ($this->countLinks === 0) {
                $this->deny();
            }
        }
    }

    /**
     * Returns number of required connections
     *
     * @return int
     */
    public function getCountConnections()
    {
        return $this->countConnections;
    }

    /**
     * Returns the low-level connection implementation
     *
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Returns the database implementation
     *
     * @return \go\DB\Implementations\Base
     */
    public function getImplementation()
    {
        return $this->implementation;
    }

    /**
     * Closes all connection
     */
    protected function deny()
    {
        if ($this->connection) {
            $this->implementation->close($this->connection);
            $this->connection = null;
        }
        $this->countLinks = 0;
        $this->countConnections = 0;
        return true;
    }

    /**
     * The database implementation
     *
     * @var \go\DB\Implementations\Base
     */
    private $implementation;

    /**
     * The low-level connection implementation
     *
     * @var mixed
     */
    private $connection;

    /**
     * The connection configuration
     *
     * @var array
     */
    private $params;

    /**
     * Number of links from DB instances
     *
     * @var int
     */
    private $countLinks;

    /**
     * Number of required connections
     *
     * @var int
     */
    private $countConnections;
}
