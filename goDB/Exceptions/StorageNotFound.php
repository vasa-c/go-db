<?php
/**
 * Исключение: база с таким именем отсутствует в хранилище
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       http://code.google.com/p/go-ns/wiki/go_DB_Exceptions_StorageNotFound
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class StorageNotFound extends Storage
{
    protected $MESSAGE_PATTERN = 'DB with name "{{ dbname }}" not found in Storage';
}