<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: adatabase with this name is already engaged in this storage
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class StorageEngaged extends Storage
{
    protected $MESSAGE_PATTERN = 'Name "{{ dbname }}" already engaged in Storage';
}
