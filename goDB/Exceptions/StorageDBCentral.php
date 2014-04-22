<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: the main database is not defined in this storage
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class StorageDBCentral extends Storage
{
    protected $MESSAGE_PATTERN = 'Storage contains no central database';
}
