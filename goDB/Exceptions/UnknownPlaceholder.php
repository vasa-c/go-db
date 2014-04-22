<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: a placeholder is unknown
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class UnknownPlaceholder extends Placeholder
{
    /**
     * {@inheritdoc}
     */
    protected $MESSAGE_PATTERN = 'Unknown placeholder "{{ placeholder }}"';
}
