<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers\Iterators;

/**
 * The iterator for numerics()
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Numerics extends Base
{
    /**
     * {@inheritdoc}
     */
    public function fetchNextRow()
    {
        return $this->implementation->fetchRow($this->connection, $this->cursor);
    }
}
