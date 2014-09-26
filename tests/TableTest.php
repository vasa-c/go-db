<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB;

use go\DB\DB;

/**
 * @coversDefaultClass go\DB\Table
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class TableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $createSQL = '
        CREATE TABLE `pr_test` (
            `id_s` INTEGER,
            `a` INTEGER,
            `b_s` INTEGER,
            PRIMARY KEY (`id_s`)
        );
    ';

    /**
     * @var array
     */
    private $rows = array(
        array(1, 1, 2),
        array(2, 1, 4),
        array(3, 1, 6),
        array(4, 2, 8),
        array(5, 2, 10),
    );

    /**
     * @var array
     */
    private $map = array(
        'id' => 'id_s',
        'b' => 'b_s',
    );

    /**
     * @param boolean $fill [optional]
     * @return \go\DB\DB
     */
    private function createDB($fill = true)
    {
        if (!\extension_loaded('sqlite3')) {
            $this->markTestSkipped('SQLite3 not loaded');
        }
        $params = array(
            '_adapter' => 'sqlite',
            '_prefix' => 'pr_',
            'filename' => ':memory:',
            'mysql_quot' => true,
        );
        $db = DB::create($params);
        if ($fill) {
            $db->query($this->createSQL);
            foreach ($this->rows as $row) {
                $pattern = 'INSERT INTO `pr_test` VALUES (?l)';
                $data = array($row);
                $db->query($pattern, $data);
            }
        }
        return $db;
    }

    /**
     * @covers \go\DB\DB::getTable
     */
    public function testCreate()
    {
        $db = $this->createDB(false);
        $table = $db->getTable('test', $this->map);
        $this->assertInstanceOf('go\DB\Table', $table);
    }

    /**
     * @covers ::getTableName
     */
    public function testGetTableName()
    {
        $db = $this->createDB(false);
        $this->assertSame('test', $db->getTable('test')->getTableName());
        $this->assertSame('unk', $db->getTable('unk')->getTableName());
    }

    /**
     * @covers ::getDB
     */
    public function testGetDB()
    {
        $db = $this->createDB(false);
        $this->assertSame($db, $db->getTable('test')->getDB());
    }

    /**
     * @covers ::getCount
     */
    public function testGetCount()
    {
        $db = $this->createDB(true);
        $table = $db->getTable('test', $this->map);
        $db->query('UPDATE `pr_test` SET `b_s`=NULL WHERE `id_s`=3');
        $this->assertSame(5, $table->getCount());
        $this->assertSame(5, $table->getCount('a'));
        $this->assertSame(4, $table->getCount('b'));
        $this->assertSame(3, $table->getCount(null, array('a' => 1)));
        $this->assertSame(2, $table->getCount('b', array('a' => 1)));
    }

    /**
     * @covers ::insert
     */
    public function testInsert()
    {
        $db = $this->createDB(true);
        $table = $db->getTable('test', $this->map);
        $id = $table->insert(array('a' => 5, 'b' => 6));
        $this->assertNotEmpty($id);
        $row = $db->query('SELECT `id_s`,`a`,`b_s` FROM `pr_test` WHERE `id_s`=?i', array($id))->row();
        $this->assertEquals(array('id_s' => $id, 'a' => 5, 'b_s' => 6), $row);
    }


    /**
     * @covers ::multiInsert
     */
    public function testMultiInsert()
    {
        $db = $this->createDB(true);
        $table = $db->getTable('test', $this->map);
        $values = array(
            array(
                'a' => 2,
                'b' => 4,
            ),
            array(
                'a' => 3,
                'b' => 6,
            ),
        );
        $table->multiInsert($values, false);
        $actual = $db->query('SELECT `a`,`b_s` FROM `pr_test` WHERE `id_s`>5')->vars();
        $expected = array(
            2 => 4,
            3 => 6,
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::replace
     * @covers ::multiReplace
     */
    public function testMultiReplace()
    {
        $db = $this->createDB(true);
        $table = $db->getTable('test', $this->map);
        $values = array(
            array(
                'id' => 2,
                'a' => 17,
            ),
            array(
                'id' => 22,
                'a' => 24,
            ),
        );
        $table->multiReplace($values, false);
        $actual = $db->query('SELECT `id_s`,`a` FROM `pr_test`')->vars();
        $expected = array(
            1 => 1,
            2 => 17,
            3 => 1,
            4 => 2,
            5 => 2,
            22 => 24,
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $db = $this->createDB(true);
        $table = $db->getTable('test', $this->map);
        $table->update(array('b' => 11), array('a' => 2));
        $actual = $db->query('SELECT `id_s`,`b_s` FROM `pr_test`')->vars();
        $expected = array(
            1 => 2,
            2 => 4,
            3 => 6,
            4 => 11,
            5 => 11,
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $db = $this->createDB(true);
        $table = $db->getTable('test', $this->map);
        $this->assertEquals(2, $table->delete(array('a' => 2)));
        $this->assertEquals(3, $db->query('SELECT COUNT(1) FROM `pr_test`')->el());
        $this->assertEquals(3, $table->delete());
        $this->assertEquals(0, $table->delete());
        $this->assertEquals(0, $db->query('SELECT COUNT(1) FROM `pr_test`')->el());
    }

    /**
     * @covers ::select
     */
    public function testSelect()
    {
        $db = $this->createDB(true);
        $last = null;
        $debug = function ($query) use (&$last) {
            $last = $query;
        };
        $db->setDebug($debug);
        $table = $db->getTable('test', $this->map);
        $expected = array(
            1 => array('id' => 1, 'a' => 1, 'b' => 2),
            2 => array('id' => 2, 'a' => 1, 'b' => 4),
            3 => array('id' => 3, 'a' => 1, 'b' => 6),
            4 => array('id' => 4, 'a' => 2, 'b' => 8),
            5 => array('id' => 5, 'a' => 2, 'b' => 10),
        );
        $this->assertEquals($expected, $table->select()->assoc('id'));
        $this->assertSame('SELECT * FROM "pr_test" WHERE 1=1', $last);
        $expected = array(
            2 => array('a' => 1, 'b' => 2),
            4 => array('a' => 1, 'b' => 4),
            6 => array('a' => 1, 'b' => 6),
            8 => array('a' => 2, 'b' => 8),
            10 => array('a' => 2, 'b' => 10),
        );
        $res = $table->select(array('a', 'b'));
        $this->assertEquals($expected, $res->assoc('b'));
        $this->assertSame('SELECT "a","b_s" FROM "pr_test" WHERE 1=1', $last);
        $expected = array(
            2 => array('b' => 2),
            4 => array('b' => 4),
            6 => array('b' => 6),
            8 => array('b' => 8),
            10 => array('b' => 10),
        );
        $res = $table->select('b');
        $this->assertSame('SELECT "b_s" FROM "pr_test" WHERE 1=1', $last);
        $this->assertEquals($expected, $res->assoc('b'));
        $expected = array(
            8 => array('b' => 8),
            10 => array('b' => 10),
        );
        $res = $table->select('b', array('a' => 2));
        $this->assertEquals($expected, $res->assoc('b'));
        $this->assertSame('SELECT "b_s" FROM "pr_test" WHERE "a"=2', $last);
        $expected = array(
            array('b' => 8),
            array('b' => 10),
            array('b' => 2),
            array('b' => 4),
            array('b' => 6),
        );
        $res = $table->select(array('b'), true, array('a' => false, 'b' => true));
        $this->assertEquals($expected, $res->assoc());
        $this->assertSame('SELECT "b_s" FROM "pr_test" WHERE 1=1 ORDER BY "a" DESC,"b_s" ASC', $last);
        $expected = array(1, 2, 3);
        $res = $table->select('id', null, 'id', 3);
        $this->assertEquals($expected, $res->col());
        $this->assertSame('SELECT "id_s" FROM "pr_test" WHERE 1=1 ORDER BY "id_s" ASC LIMIT 0,3', $last);
        $expected = array(3, 4, 5);
        $res = $table->select('id', null, 'id', array(3, 2));
        $this->assertEquals($expected, $res->col());
        $this->assertSame('SELECT "id_s" FROM "pr_test" WHERE 1=1 ORDER BY "id_s" ASC LIMIT 2,3', $last);
    }

    /**
     * @covers ::getMap
     */
    public function testGetMap()
    {
        $db = $this->createDB(false);
        $table = $db->getTable('test', $this->map);
        $map = $table->getMap();
        $this->assertInstanceOf('go\DB\Helpers\MapFields', $map);
        $this->assertEquals($this->map, $map->getMap());
        $table = $db->getTable('test');
        $this->assertNull($table->getMap());
    }
}
