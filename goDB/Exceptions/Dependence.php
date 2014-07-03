<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: lack the requisite dependence
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Dependence extends Logic
{
    /**
     * The constructor
     *
     * @param string $dep [optional]
     */
    public function __construct($dep = null)
    {
        $message = 'Required '.$dep;
        parent::__construct($message, 0);
    }
}
