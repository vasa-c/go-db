<?php
/**
 * Исключение: хранилище не имеет центральной базы
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       http://code.google.com/p/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class StorageDBCentral extends Storage
{
    protected $MESSAGE_PATTERN = 'Storage contains no central database';
}