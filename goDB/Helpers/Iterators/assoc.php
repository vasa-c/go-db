<?php
/**
 * Итератор по assoc
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers\Iterators;

final class assoc extends Base
{
    /**
     * @override Base
     *
     * @return mixed | false
     */
    public function fetchNextRow() {
        return $this->implementation->fetchAssoc($this->connection, $this->cursor);
    }

}