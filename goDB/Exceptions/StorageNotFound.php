<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: a required database was not found in the storage
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class StorageNotFound extends Storage
{
    protected $MESSAGE_PATTERN = 'DB with name "{{ dbname }}" not found in Storage';
}
