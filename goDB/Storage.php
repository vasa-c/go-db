<?php
/**
 * @package go\DB
 */

namespace go\DB;

use go\DB\Exceptions\StorageEngaged;
use go\DB\Exceptions\StorageAssoc;
use go\DB\Exceptions\StorageDBCentral;

/**
 * Storage for database instances
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Storage
{
    /**
     * Returns the main storage instance
     *
     * @return \go\DB\Storage
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Sets a storage instance as main
     *
     * @param mixed $instance
     *        a storage instance or its parameters
     * @return \go\DB\Storage
     * @todo why trigger_error() instead exception?
     */
    public static function setInstance($instance)
    {
        if ($instance instanceof self) {
            self::$instance = $instance;
        } elseif (\is_array($instance)) {
            self::$instance = new self($instance);
        } else {
            $message = 'Argument 1 passed to Storage::setInstance must be '.
                'an instance of go\DB\DB or array, '.\gettype($instance).' given';
            \trigger_error($message, \E_USER_ERROR);
        }
        return self::$instance;
    }

    /**
     * A query to the main database of the main storage
     *
     * @param string $pattern
     * @param array $data [optional]
     * @param string $fetch [optional]
     * @param string $prefix [optional]
     * @throws \go\DB\Exceptions\StorageDBCentral
     *         main storage is not exists
     * @throws \go\DB\Exceptions\Connect
     * @throws \go\DB\Exceptions\Closed
     * @throws \go\DB\Exceptions\Templater
     * @throws \go\DB\Exceptions\Query
     * @throws \go\DB\Exceptions\Fetch
     */
    public static function query($pattern, $data = null, $fetch = null, $prefix = null)
    {
        return self::getInstance()->__invoke($pattern, $data, $fetch, $prefix);
    }

    /**
     * The constructor of a storage
     *
     * @param array $mparams [optional]
     *        parameters for filling (empty storage by default)
     * @param string $mainname [optional]
     *        the name of the main database in this storage
     * @throws \go\DB\Exceptions\StorageAssoc
     *         mparams has an invalid association
     */
    public function __construct($mparams = null, $mainname = '')
    {
        if ($mparams) {
            $this->fill($mparams);
        }
        $this->mainname = (string)$mainname;
        return true;
    }

    /**
     * Returns a database instance by its name
     *
     * @param string $name [optional]
     *        the name (main database by default)
     * @return \go\DB\DB
     * @throws \go\DB\Exceptions\StorageNotFound
     *         this name does not exists in the storage
     */
    public function get($name = null)
    {
        if ($name === null) {
            $name = $this->mainname;
        }
        if (!isset($this->dbs[$name])) {
            throw new Exceptions\StorageNotFound($name);
        }
        return $this->dbs[$name];
    }

    /**
     * Creates a database instance and saves it in the storage
     *
     * @param array $params
     *        the connection parameters
     * @param string $name [optional]
     *        the name in the storage (main by default)
     * @return \go\DB\DB
     *         the database instance
     * @throws \go\DB\Exceptions\StorageEngaged
     *         this name is alreagy engaged
     * @throws \go\DB\Exceptions\Config
     * @throws \go\DB\Exceptions\Connect
     */
    public function create(array $params, $name = null)
    {
        if ($name === null) {
            $name = $this->mainname;
        }
        $db = DB::create($params);
        $this->set($db, $name);
        return $db;
    }

    /**
     * Saves a database in the storage
     *
     * @param \go\DB\DB $db
     * @param string $name [optional]
     * @throws \go\DB\Exceptions\StorageEngaged
     *         this name is alreagy engaged
     */
    public function set(DB $db, $name = null)
    {
        if ($name === null) {
            $name = $this->mainname;
        }
        if (isset($this->dbs[$name])) {
            throw new StorageEngaged($name);
        }
        $this->dbs[$name] = $db;
        return true;
    }

    /**
     * Fills the storage by databases
     *
     * @param array $mparams
     *        parameters for create databases
     * @throws \go\DB\Exceptions\StorageAssoc
     * @throws \go\DB\Exceptions\StorageEngaged
     */
    public function fill(array $mparams)
    {
        $assocs = array();
        foreach ($mparams as $name => $params) {
            if (\is_array($params)) {
                $this->create($params, $name);
            } elseif (isset($mparams[$params])) {
                $assocs[$name] = $params;
            } else {
                throw new StorageAssoc($params);
            }
        }
        foreach ($assocs as $name => $assoc) {
            $this->set($this->get($assoc), $name);
        }
        return true;
    }

    /**
     * Checks if a name exists in the storage
     *
     * @param string $name [optional]
     * @return bool
     */
    public function exists($name = null)
    {
        if ($name === null) {
            $name = $this->mainname;
        }
        return isset($this->dbs[$name]);
    }

    /**
     * Invoke: query to the main database
     *
     * @param string $pattern [optional]
     * @param array $data [optional]
     * @param string $fetch [optional]
     * @param string $prefix [optional]
     * @return mixed
     * @throws \go\DB\Exceptions\StorageDBCentral
     * @throws \go\DB\Exceptions\Connect
     * @throws \go\DB\Exceptions\Closed
     * @throws \go\DB\Exceptions\Templater
     * @throws \go\DB\Exceptions\Query
     * @throws \go\DB\Exceptions\Fetch
     */
    public function __invoke($pattern, $data = null, $fetch = null, $prefix = null)
    {
        if (!isset($this->dbs[$this->mainname])) {
            throw new StorageDBCentral('');
        }
        return $this->dbs[$this->mainname]->query($pattern, $data, $fetch, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function __set($key, $value)
    {
        return $this->set($value, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($key)
    {
        return $this->exists($key);
    }

    /**
     * Returns the main database of this storage
     *
     * @return \go\DB\DB
     * @throws \go\DB\Exceptions\StorageNotFound
     */
    public function getMainDB()
    {
        return $this->get(null);
    }

    /**
     * The main storage
     *
     * @var \go\DB\Storage
     */
    private static $instance;

    /**
     * The list of databases
     *
     * @var array
     */
    private $dbs = array();

    /**
     * The name of the main database
     *
     * @var string
     */
    private $mainname = '';
}
