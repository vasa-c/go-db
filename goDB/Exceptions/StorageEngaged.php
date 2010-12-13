<?php
/**
 * Исключение: имя базы занято в хранилище
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       http://code.google.com/p/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class StorageEngaged extends Storage
{
    protected $MESSAGE_PATTERN = 'Name "{{ dbname }}" already engaged in Storage';
}