<?php
/**
 * @package go\DB
 */

namespace go\DB\Adapters;

use go\DB\DB;
use go\DB\Table;

/**
 * The adapter for "fake" DB
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Fake extends DB
{
    /**
     * {@inheritdoc}
     */
    public function getTable($tableName, array $map = null)
    {
        $fake = $this->getImplementationConnection()->getTable($tableName);
        return new Table($this, $fake, $map);
    }
}
