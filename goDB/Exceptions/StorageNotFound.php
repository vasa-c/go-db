<?php
/**
 * Исключение: база с таким именем отсутствует в хранилище
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class StorageNotFound extends Storage
{
    protected $MESSAGE_PATTERN = 'DB with name "{{ dbname }}" not found in Storage';
}