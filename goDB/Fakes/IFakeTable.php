<?php
/**
 * @package go\DB
 */

namespace go\DB\Fakes;

/**
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
interface IFakeTable
{
    /**
     * @param array $set
     * @return int
     */
    public function insert(array $set);

    /**
     * @param array $sets
     */
    public function multiInsert(array $sets);

    /**
     * @param array $set
     */
    public function replace(array $set);

    /**
     * @param array $sets
     */
    public function multiReplace(array $sets);

    /**
     * @param array $set
     * @param $where
     * @return int
     */
    public function update(array $set, $where);

    /**
     * @param mixed $cols
     * @param mixed $where
     * @param mixed $order
     * @param mixed $limit
     * @return \go\DB\Result
     */
    public function select($cols, $where, $order, $limit);

    /**
     * @param mixed $where
     * @return mixed
     */
    public function delete($where);

    public function truncate();

    /**
     * @param mixed $col
     * @param mixed $where
     * @return int
     */
    public function getCount($col, $where);
}
