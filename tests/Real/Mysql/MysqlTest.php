<?php
/**
 * @package go\DB
 */

namespace go\Tests\DB\Real\Mysql;

use go\Tests\DB\Real\Base;

class MysqlTest extends Base
{
    /**
     * {@inheritdoc}
     */
    protected $adapter = 'mysql';

    /**
     * {@inheritdoc}
     */
    protected $reqExt = 'mysqli';

    public function testDB()
    {
        $db = $this->createDB(__DIR__.'/dump.sql');
        $this->assertEquals(5, $db->query('SELECT COUNT(`id`) FROM `godbtest`')->el());
        $this->assertEquals(4, $db->query('SELECT COUNT(`val`) FROM `godbtest`')->el());
        $set = array(
            'num' => 8,
            'desc' => 1,
            'val' => 'six',
        );
        $this->assertEquals(6, $db->query('INSERT INTO `godbtest` SET ?s', array($set))->id());
        $set = array(
            'num' => 9,
            'desc' => 1,
            'val' => null,
        );
        $this->assertEquals(7, $db->query('INSERT INTO `godbtest` SET ?s', array($set))->id());
        $expected = array(
            array(6, 1, 8, 'six'),
            array(7, 1, 9, null),
            array(5, 2, 7, 'five'),
            array(4, 3, 7, null),
            array(2, 6, 3, 'two'),
            array(3, 6, 3, 'three'),
            array(1, 10, 1, 'one'),
        );
        $sql = 'SELECT `id`,`desc`,`num`,`val` FROM `godbtest` ORDER BY `desc` ASC, `id` ASC';
        $actual = $db->query($sql)->numerics();
        $this->assertEquals($expected, $actual);
        $this->assertNull($actual[1][3]);
        $this->assertNull($actual[3][3]);

        $sql = 'SELECT COUNT(*) FROM `godbtest` WHERE ?w';
        $this->assertTrue($db->query($sql, array(array()))->el() > 0);
        $this->assertTrue($db->query($sql, array(null))->el() > 0);
        $this->assertTrue($db->query($sql, array(true))->el() > 0);

        $this->assertTrue($db->query($sql, array(false))->el() == 0);
    }

    /**
     * @covers \go\DB\Table::startAccumInsert
     * @covers \go\DB\Table::flustAccumInsert
     */
    public function testAccumInsert()
    {
        $db = $this->createDB(__DIR__.'/accum.sql');
        $table = $db->getTable('godbaccumtest');
        $table->startAccumInsert(2, 3);
        $this->assertSame(3, $table->insert(array('a' => 3, 'b' => 6)));
        $this->assertSame(4, $table->insert(array('a' => 4, 'b' => 8)));
        $this->assertEquals(0, $table->getCount());
        $this->assertSame(5, $table->insert(array('a' => 5, 'b' => 10)));
        $this->assertEquals(3, $table->getCount());
        $this->assertSame(6, $table->insert(array('a' => 6, 'b' => 12)));
        $this->assertEquals(3, $table->getCount());
        $this->assertSame(1, $table->flushAccumInsert());
        $this->assertSame(0, $table->flushAccumInsert());
        $this->assertEquals(4, $table->getCount());
        $this->assertSame(5, $table->insert(array('a' => 7, 'b' => 14)));
        $this->assertSame(0, $table->flushAccumInsert());
        $this->assertEquals(5, $table->getCount());
        $actual = $table->select(array('id', 'a', 'b'), null, 'id')->numerics();
        $expected = array(
            array(1, 3, 6),
            array(2, 4, 8),
            array(3, 5, 10),
            array(4, 6, 12),
            array(5, 7, 14),
        );
        $this->assertEquals($expected, $actual);
    }

    public function testPreQuery()
    {
        $db = $this->createDB();
        $db->preQuery('SET @a="q"');
        $this->assertFalse($db->isConnected());
        $this->assertSame('q', $db->query('SELECT @a')->el());
        $this->assertTrue($db->isConnected());
    }
}
