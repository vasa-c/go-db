<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * The basic exception class of the library
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
interface Exception
{
    /**
     * @return array
     */
    public function getTrace();

    /**
     * @return string
     */
    public function getFile();

    /**
     * @return int
     */
    public function getLine();
}
