<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers\Debuggers;

/**
 * The debugger for the console
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class OutConsole
{
    /**
     * @param string $query
     * @param float $duration
     * @param mixed $info
     */
    public function __invoke($query, $duration, $info)
    {
        echo $query.\PHP_EOL;
    }
}
