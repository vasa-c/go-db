<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: unexpected a fetch format for this context
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class UnexpectedFetch extends Fetch
{
    protected $MESSAGE_PATTERN = 'Unexpected format "{{ fetch }}" for this context';
}
