<?php
/**
 * Итератор по numerics
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers\Iterators;

final class Numerics extends Base
{
    /**
     * @override Base
     *
     * @return mixed | false
     */
    public function fetchNextRow()
    {
        return $this->implementation->fetchRow($this->connection, $this->cursor);
    }
}
