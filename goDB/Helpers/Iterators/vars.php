<?php
/**
 * Итератор по vars
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers\Iterators;

final class vars extends Base
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
        return $this->nextRow[0];
    }

    /**
     * @override Base
     *
     * @return mixed
     */
    public function current() {
        return \array_key_exists('1', $this->nextRow) ? $this->nextRow[1] : $this->nextRow[0];
    }
}