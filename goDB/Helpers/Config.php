<?php
/**
 * Получение конфигурации goDB (из каталога _config)
 *
 * @package    go\DB
 * @subpackage config
 * @author     Григорьев Олег aka vasa_c
 * @static
 * @protected для использования внутри библиотеки
 */

namespace go\DB\Helpers;

final class Config
{
    /**
     * Получить нужную конфигурацию
     *
     * @example \go\DB\Config::get('placeholders')
     *
     * @param string $name
     * @return mixed
     */
    public static function get($name) {
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
     * Уже загруженные конфигурации
     *
     * @var array
     */
    private static $config = array();
}