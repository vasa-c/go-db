<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Fakes;

use go\DB\DB;

/**
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class FakesDBTest extends \PHPUnit_Framework_TestCase
{
    private $params = [
        '_adapter' => 'fake',
        'tables' => [
            'one' => [
                'pk' => 'id',
                'lastAI' => 3,
                'defaults' => [
                    'enabled' => 1,
                ],
                'data' => [
                    ['id' => 1, 'name' => 'A', 'enabled' => 0],
                    ['id' => 2, 'name' => 'B', 'enabled' => 1],
                    ['id' => 3, 'name' => 'C', 'enabled' => 1],
                ],
            ],
            'two' => [
                'data' => [
                    ['a' => 1],
                    ['b' => 2],
                ],
            ],
        ],
    ];

    public function testDB()
    {
        $db = DB::create($this->params);
        $list = $db->query('SHOW TABLES')->col();
        $this->assertSame(['one', 'two'], $list);
        $this->assertSame(2, $db->getTable('two')->getCount());
        $table1 = $db->getTable('one');
        $table2 = $db->getTable('one', ['title' => 'name']);
        $this->assertNotSame($table1, $table2);
        $this->assertSame(1, $table1->delete(['id' => 2]));
        $this->assertSame(4, $table1->insert(['name' => 'D']));
        $expected = [
            ['id' => 4, 'title' => 'D'],
            ['id' => 3, 'title' => 'C'],
        ];
        $actual = $table2->select(['id', 'title'], ['enabled' => 1], ['id' => false])->assoc();
        $this->assertEquals($expected, $actual);
        $imp = $db->getImplementationConnection()->getListTables();
        $this->assertSame(4, $imp['one']->getLastIncrement());
        $this->setExpectedException('go\DB\Exceptions\Query');
        $db->query('SELECT * FROM `one`');
    }
}
