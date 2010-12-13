<?php
/**
 * Исключение: при заполнении ассоциируемая база не существует
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       http://code.google.com/p/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class StorageAssoc extends Storage
{
    protected $MESSAGE_PATTERN = 'Association error: "{{ dbname }}" not found in params';
}