<?php
/**
 * Реализация представления результата запроса
 *
 * @package    go\Db
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers;

use go\DB\Implementations\Base as Implementation;

class Fetcher implements \go\DB\Result
{
    /**
     * Конструктор
     *
     * @param Implementation $implementation
     * @param mixed $cursor
     */
    public function __construct(Implementation $implementation, $cursor) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\Fetch
     *
     * @param string $fetch
     * @return mixed
     */
    public function fetch($fetch) {

    }

    /**
     * @override \go\DB\Result
     */
    public function free() {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param string $param [optional]
     * @return array
     */
    public function assoc($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param int $param [optional]
     * @return array
     */
    public function numerics($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param string $param [optional]
     * @return array
     */
    public function objects($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return array
     */
    public function col($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return array
     */
    public function vars($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param string $param [optional]
     * @return Iterator
     */
    public function iassoc($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param int $param [optional]
     * @return Iterator
     */
    public function inumerics($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param string $param [optional]
     * @return Iterator
     */
    public function iobjects($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return Iterator
     */
    public function ivars($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return Iterator
     */
    public function icol($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return array
     */
    public function row($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return array
     */
    public function numeric($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return object
     */
    public function object($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return string
     */
    public function el($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return bool
     */
    public function bool($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return int
     */
    public function num($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @return int
     */
    public function id($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @return int
     */
    public function ar($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @return mixed
     */
    public function cursor($param = null) {

    }

    /**
     * @override \go\DB\Result
     *
     * @return \Iterator
     */
    public function getIterator() {

    }
}