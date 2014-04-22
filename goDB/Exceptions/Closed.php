<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: you cannot work with a hard-closed connection
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Closed extends Logic
{
    /**
     * The constructor
     */
    public function __construct()
    {
        $message = 'Connection is closed';
        parent::__construct($message, 0);
    }
}
