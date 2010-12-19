<?php
/**
 * Итератор по objects
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers\Iterators;

final class objects extends Base
{
    /**
     * @override Base
     *
     * @return mixed | false
     */
    public function fetchNextRow() {
        return $this->implementation->fetchAssoc($this->connection, $this->cursor);
    }

    /**
     * @override Base
     *
     * @return mixed
     */
    public function current() {
        return (object)$this->nextRow;
    }
}