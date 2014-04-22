<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: a pattern contain regular and named placeholders
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class MixedPlaceholder extends Placeholder
{
    /**
     * {@inheritdoc}
     */
    protected $MESSAGE_PATTERN = 'Mixed placeholder "{{ placeholder }}"';
}
