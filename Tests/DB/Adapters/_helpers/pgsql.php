<?php
/**
 * Хелпер создания тестовых таблиц (адаптер pgsql)
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Alex Polev
 */

namespace go\Tests\DB\Adapters\_helpers;


class pgsql extends Base
{

    protected $testTypes = array(
        'ID'     => 'SERIAL',
        'STRING' => 'VARCHAR(200) NULL DEFAULT NULL',
        'INT'    => 'INT NULL DEFAULT NULL',
    );

    protected $testKeys = array(
        'primary' => 'PRIMARY KEY (?cols)',
    );

	/**
     * Структуры тестовых таблиц
     *
     * @var array
     */
    protected $testTables = array(
        'test_table' => array(
            'cols' => array(
                'name'   => 'STRING',
                'number' => 'INT',
                'id'     => 'ID',
            ),
            'keys' => array(
                'primary' => 'id',
            ),
            'data' => array(
                array('one',   2),
                array('two',   null),
                array('three', 6),
                array('four',  null),
                array('five',  10),
            ),
        ),
        'test_vars' => array(
            'cols' => array(
                'key'   => 'STRING',
                'value' => 'STRING',
            ),
            'keys' => array(
                'primary' => array('key', 'value'),
            ),
            'data' => array(
                array('name', 'goDB'),
                array('pi',   '3,14'),
                array('e',    '2,7'),
                array('year', '2010'),
                array('three', 33),
            ),
        ),
    );
}