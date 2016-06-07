<?php
/**
 * @package go\DB
 */

namespace go\Tests\DB\Real\Sqlite;

use go\Tests\DB\Real\Base;
use go\DB\DB;
use go\DB\Exceptions\Connect;

class SqliteTest extends Base
{
    /**
     * {@inheritdoc}
     */
    protected $adapter = 'sqlite';

    /**
     * {@inheritdoc}
     */
    protected $reqExt = 'sqlite3';

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
        $data = array(array_keys($set), array_values($set));
        $this->assertEquals(6, $db->query('INSERT INTO `godbtest` (?cols) VALUES (?list)', $data)->id());
        $set = array(
            'num' => 9,
            'desc' => 1,
            'val' => null,
        );
        $data = array(array_keys($set), array_values($set));
        $this->assertEquals(7, $db->query('INSERT INTO `godbtest` (?cols) VALUES (?list)', $data)->id());
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

        $trickyName = 'qu`ote"d na\'me';
        $this->assertEquals('str', $db->query("SELECT 'str' as ?c", array($trickyName))->el($trickyName));
    }

    public function testErrorConnect()
    {
        $this->loadConnectionParams();
        $params = [
            'filename' => __DIR__.'/1.txt',
            'flags' => 1, // SQLITE3_OPEN_READONLY,
            '_lazy' => false,
        ];
        try {
            DB::create($params, 'sqlite');
            $this->fail('not thrown');
        } catch (Connect $e) {
            $this->assertNotEmpty($e->getMessage());
        }
    }
}
