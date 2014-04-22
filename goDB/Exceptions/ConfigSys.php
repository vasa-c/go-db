<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: system parameters are invalid
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class ConfigSys extends Config
{
    /**
     * The constructor
     *
     * @param string $message [optional]
     */
    public function __construct($message = null)
    {
        $message = 'Error system config'.($message ? (': "'.$message.'"') : '');
        parent::__construct($message, 0);
    }
}
