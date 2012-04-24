<?php
/**
 * Исключение: хранилище не имеет центральной базы
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       https://github.com/vasa-c/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class StorageDBCentral extends Storage
{
    protected $MESSAGE_PATTERN = 'Storage contains no central database';
}