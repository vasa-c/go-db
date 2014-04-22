<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers\Iterators;

/**
 * The iterator for vars()
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Vars extends Base
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
        return $this->nextRow[0];
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return \array_key_exists('1', $this->nextRow) ? $this->nextRow[1] : $this->nextRow[0];
    }
}
