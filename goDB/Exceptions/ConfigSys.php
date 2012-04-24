<?php
/**
 * Исключение: ошибка конфигурации - неверные системные параметры
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       https://github.com/vasa-c/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class ConfigSys extends Config
{
    public function __construct($message = null) {
        $message = 'Error system config'.($message ? (': "'.$message.'"') : '');
        parent::__construct($message, 0);
    }
}