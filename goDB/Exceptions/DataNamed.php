<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: a required named value was not found
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class DataNamed extends Data
{
    /**
     * The constructor
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $message = 'Named data "'.$name.'" is not found';
        parent::__construct($message);
    }
}
