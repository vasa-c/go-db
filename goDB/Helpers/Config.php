<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers;

/**
 * Access for the goDB-configuration (from the directory _config)
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @protected for internal use
 */
final class Config
{
    /**
     * Returns a specified configuration
     *
     * @example \go\DB\Config::get('placeholders')
     *
     * @param string $name
     * @return mixed
     * @throws \RuntimeException
     */
    public static function get($name)
    {
        if (!isset(self::$config[$name])) {
            $filename = __DIR__.'/../_config/'.$name.'.php';
            if (!\file_exists($filename)) {
                throw new \RuntimeException('Error go\\DB config "'.$name.'"');
            }
            self::$config[$name] = include($filename);
        }
        return self::$config[$name];
    }

    /**
     * The cache of loaded configurations
     *
     * @var array
     */
    private static $config = array();
}
