<?php
/**
 * Итератор по col
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers\Iterators;

final class col extends Base
{
    /**
     * @override Base
     *
     * @return mixed | false
     */
    public function fetchNextRow() {
        return $this->implementation->fetchRow($this->connection, $this->cursor);
    }

    /**
     * @overrider Base
     *
     * @return string
     */
    public function key() {
        if (!$this->nextRow) {
            return false;
        }
        return $this->pointer;
    }

    /**
     * @override Base
     *
     * @return mixed
     */
    public function current() {
        return $this->nextRow[0];
    }
}