<?php
/**
 * @package go\DB
 */

namespace go\DB;

/**
 * Compatibility with older versions
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Compat
{
    /**
     * Sets a compatibility option
     *
     * @param string $key
     * @param mixed $value
     */
    public static function setOpt($key, $value)
    {
        self::$opts[$key] = $value;
    }

    /**
     * Sets a list of compatibility options for the current connection
     *
     * @param array $opts
     */
    public static function setCurrentOpts(array $opts)
    {
        self::$current = $opts;
    }

    /**
     * Returns a compatibility option by its key
     *
     * @param string $key
     * @param mixed $default [optional]
     * @return mixed
     */
    public static function getOpt($key, $default = null)
    {
        if (\array_key_exists($key, self::$current)) {
            return self::$current[$key];
        }
        if (\array_key_exists($key, self::$opts)) {
            return self::$opts[$key];
        }
        return $default;
    }

    /**
     * @var array
     */
    private static $opts = array(
        'null' => true,
    );

    /**
     * @var array
     */
    private static $current = array();
}
