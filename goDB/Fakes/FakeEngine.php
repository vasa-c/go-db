<?php
/**
 * @package go\DB
 */

namespace go\DB\Fakes;

use go\DB\Implementations\TestBase\Cursor;

/**
 * The "engine" of the fake database
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class FakeEngine
{
    const ERROR_QUERY = 1;

    /**
     * The constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $defaults = array(
            'data' => array(),
            'lastAI' => null,
            'pk' => null,
            'defaults' => null,
        );
        foreach ($config['tables'] as $k => $table) {
            if (is_array($table)) {
                $table = array_replace($defaults, $table);
                $table = new FakeTable($table['data'], $table['defaults'], $table['pk'], $table['lastAI']);
            }
            $this->tables[$k] = $table;
        }
    }

    /**
     * Performs a query
     *
     * @param string $query
     * @return \go\DB\Implementations\TestBase\Cursor
     */
    public function query($query)
    {
        $this->errorInfo = null;
        $this->errorCode = null;
        $query = trim(strtolower($query));
        if ($query === 'show tables') {
            $data = array();
            foreach ($this->tables as $k => $table) {
                $data[] = array('name' => $k);
            }
            return new Cursor($data);
        }
        $this->errorInfo = 'Invalid query';
        $this->errorCode = self::ERROR_QUERY;
    }

    /**
     * Closes a "connection"
     */
    public function close()
    {
        $this->closed = true;
        return true;
    }

    /**
     * Checks if a connection is closed
     *
     * @return bool
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * @return string
     */
    public function getErrorInfo()
    {
        return $this->errorInfo;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return int
     */
    public function getInsertId()
    {
        return null;
    }

    /**
     * @return int
     */
    public function getAffectedRows()
    {
        return 0;
    }

    /**
     * @param string $name
     * @return \go\DB\Fakes\FakeTable
     */
    public function getTable($name)
    {
        if (!isset($this->tables[$name])) {
            return null;
        }
        return $this->tables[$name];
    }

    /**
     * @return \go\DB\Fakes\FakeTable[]
     */
    public function getListTables()
    {
        return $this->tables;
    }

    /**
     * @var string
     */
    private $errorInfo;

    /**
     * @var int
     */
    private $errorCode;

    /**
     * @var array
     */
    private $tables = array();

    /**
     * @var bool
     */
    private $closed = false;
}
