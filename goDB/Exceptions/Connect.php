<?php
/**
 * Исключение: не удалось подключиться к серверу (или выбрать базу)
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       https://github.com/vasa-c/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class Connect extends Runtime
{
    /**
     * Конструктор
     *
     * @param string $error
     *        описание ошибки
     * @param string $errorcode [optional]
     *        код ошибки
     */
    public function __construct($error, $errorcode = null) {
        parent::__construct($error, $errorcode);
    }
}