<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers\Debuggers;

/**
 * The debugger for browsers
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class OutHtml
{
    /**
     * @param string $query
     * @param float $duration
     * @param mixed $info
     */
    public function __invoke($query, $duration, $info)
    {
        echo '<pre>'.\htmlspecialchars($query, \ENT_COMPAT, 'utf-8').'</pre>';
    }
}
