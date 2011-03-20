<?php
/**
 * Адаптер pgsql (надстройка над php_pgsql)
 *
 * @package    go\DB
 * @subpackage Adapters
 * @author     Григорьев Олег aka vasa_c
 * @uses       php_mysqli (http://php.net/manual/en/book.mysqli.php)
 */
namespace go\DB\Adapters;

final class pgsql extends \go\DB\DB
{
    /**
     * @override Base
     *
     * @param mixed $cursor
     * @return \go\DB\Result
     */
    protected function createFetcher($cursor) {
        return (new \go\DB\Helpers\Pgfetcher($this->connector, $cursor));
    }
}