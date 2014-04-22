<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers\Iterators;

/**
 * The iterator for assoc()
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Assoc extends Base
{
    /**
     * {@inheritdoc}
     */
    public function fetchNextRow()
    {
        return $this->implementation->fetchAssoc($this->connection, $this->cursor);
    }
}
