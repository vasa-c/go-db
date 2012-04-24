<?php
/**
 * Исключение: ошибка конфигурации - неверные параметры подключения
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       https://github.com/vasa-c/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class ConfigConnect extends Config
{
    public function __construct($message = null) {
        $message = 'Error connect config'.($message ? (': "'.$message.'"') : '');
        parent::__construct($message, 0);
    }
}