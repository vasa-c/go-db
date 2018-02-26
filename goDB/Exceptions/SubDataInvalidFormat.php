<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

final class SubDataInvalidFormat extends Data
{
    /**
     * @param int $placeholder
     * @param int $message
     * @param Logic $previous
     */
    public function __construct($placeholder, $message, $previous = null)
    {
        $message = 'Invalid sub data for ?' . $placeholder . ': ' . $message;
        parent::__construct($message, 0, $previous);
    }
}
