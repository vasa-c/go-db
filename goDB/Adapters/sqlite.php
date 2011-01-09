<?php
/**
 * Адаптер sqlite
 *
 * @package    go\DB
 * @subpackage Adapters
 * @author     Григорьев Олег aka vasa_c
 * @uses       php_sqlite3 (http://php.net/manual/en/book.sqlite3.php)
 */

namespace go\DB\Adapters;

final class sqlite extends \go\DB\DB
{

    /**
     * @override \go\DB\DB
     *
     * @throws \go\DB\Exceptions\Templater
     *
     * @param string $pattern
     * @param array $data
     * @param string $prefix
     * @return string
     */
    public function makeQuery($pattern, $data, $prefix = null) {
        if (!empty($this->paramsDB['mysql_quot'])) {
            $pattern = \str_replace('`', '"', $pattern);
        }
        return parent::makeQuery($pattern, $data, $prefix);
    }

}