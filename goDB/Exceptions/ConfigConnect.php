<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: connection parameters are invalid
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class ConfigConnect extends Config
{
    /**
     * The constructor
     *
     * @param string $message [optional]
     */
    public function __construct($message = null)
    {
        $message = 'Error connect config'.($message ? (': "'.$message.'"') : '');
        parent::__construct($message, 0);
    }
}
