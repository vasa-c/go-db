<?php
/**
 * @package go\DB
 */

namespace go\DB\Adapters;

/**
 * The SQLite adapter
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @uses php_sqlite3 (http://php.net/manual/en/book.sqlite3.php)
 */
final class Sqlite extends \go\DB\DB
{
    /**
     * {@inheritdoc}
     */
    public function makeQuery($pattern, $data, $prefix = null)
    {
        if (!empty($this->paramsDB['mysql_quot'])) {
            $pattern = \str_replace('`', '"', $pattern);
        }
        return parent::makeQuery($pattern, $data, $prefix);
    }
}
