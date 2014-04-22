<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers\Iterators;

/**
 * The iterator for col()
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Col extends Base
{
    /**
     * {@inheritdoc}
     */
    public function fetchNextRow()
    {
        return $this->implementation->fetchRow($this->connection, $this->cursor);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        if (!$this->nextRow) {
            return false;
        }
        return $this->pointer;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->nextRow[0];
    }
}
