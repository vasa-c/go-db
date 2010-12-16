<?php
/**
 * Отладчик в консоль
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers\Debuggers;

final class OutConsole {

    /**
     * @param string $query
     * @param float $duration
     * @param mixed $info
     */
    public function __invoke($query, $duration, $info) {
        echo $query.\PHP_EOL;
    }

}