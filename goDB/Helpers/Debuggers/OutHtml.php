<?php
/**
 * Отладчик в браузер
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers\Debuggers;

final class OutHtml {

    /**
     * @param string $query
     * @param float $duration
     * @param mixed $info
     */
    public function __invoke($query, $duration, $info) {
        echo '<pre>'.\htmlspecialchars($query, \ENT_COMPAT, 'utf-8').'</pre>';
    }

}