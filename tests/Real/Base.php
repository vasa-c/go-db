<?php
/**
 * @package go\DB
 */

namespace go\Tests\DB\Real;

use go\DB\DB;

/**
 * The basic class for testing real database
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
abstract class Base extends \PHPUnit_Framework_TestCase
{
    /**
     * The adapter name
     * For override.
     *
     * @var string
     */
    protected $adapter;

    /**
     * Required php-extension
     * For override.
     *
     * @var string
     */
    protected $reqExt;

    /**
     * Returns connection parameters for the adapter
     *
     * @return mixed
     */
    protected function getConnectionParams()
    {
        if (!self::$dbparams) {
            if (self::$dbparams === false) {
                return null;
            }
            $filename = __DIR__.'/params.php';
            if (!\is_file($filename)) {
                self::$dbparams = false;
                return null;
            }
            self::$dbparams = include $filename;
        }
        if (!isset(self::$dbparams[$this->adapter])) {
            return null;
        }
        return self::$dbparams[$this->adapter];
    }

    /**
     * Creates a goDB instance for the current adapter
     *
     * @param string $dumpfile [optional]
     * @param array $params [optional]
     * @return \go\DB\DB
     */
    protected function createDB($dumpfile = null, $params = null)
    {
        if ($params === null) {
            $params = $this->getConnectionParams();
        }
        if ($params === null) {
            $this->markTestSkipped();
        }
        if ($this->reqExt && (!\extension_loaded($this->reqExt))) {
            $this->markTestSkipped();
        }
        $db = DB::create($params, $this->adapter);
        if ($dumpfile) {
            foreach (\explode(';', \file_get_contents($dumpfile)) as $query) {
                $query = \trim($query);
                if ($query !== '') {
                    $db->query($query);
                }
            }
        }
        return $db;
    }

    /**
     * @var array
     */
    private static $dbparams;
}
