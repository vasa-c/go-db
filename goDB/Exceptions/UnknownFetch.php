<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: fetch format is unknown
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class UnknownFetch extends Fetch
{
    /**
     * {@inheritdoc}
     */
    protected $MESSAGE_PATTERN = 'Unknown fetch format "{{ fetch }}"';
}
