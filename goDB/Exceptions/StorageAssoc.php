<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: association error for the storage filling
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class StorageAssoc extends Storage
{
    protected $MESSAGE_PATTERN = 'Association error: "{{ dbname }}" not found in params';
}
