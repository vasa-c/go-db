<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers\Iterators;

/**
 * The iterator for objects()
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Objects extends Base
{
    /**
     * {@inheritdoc}
     */
    public function fetchNextRow()
    {
        return $this->implementation->fetchAssoc($this->connection, $this->cursor);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return (object)$this->nextRow;
    }
}
