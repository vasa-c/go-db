<?php
/**
 * @package go\DB
 */

namespace go\Tests\DB\Real\Pgsql;

use go\Tests\DB\Real\Base;

class PgsqlTest extends Base
{
    /**
     * {@inheritdoc}
     */
    protected $adapter = 'pgsql';

    /**
     * {@inheritdoc}
     */
    protected $reqExt = 'pgsql';

    public function testDB()
    {
        $db = $this->createDB(__DIR__.'/dump.sql');
        $this->assertEquals(5, $db->query('SELECT COUNT("id") FROM "godbtest"')->el());
        $this->assertEquals(4, $db->query('SELECT COUNT("val") FROM "godbtest"')->el());

        $values = array(
            array(8, 1, 'six'),
            array(9, 23, '125'),
        );
        $this->assertEquals(7, $db->query('INSERT INTO "godbtest"("num", "desc", "val") VALUES ?v', array($values))->id());

        $set = array(
            'num' => 9,
            'desc' => 1,
            'val' => null,
        );
        $this->assertEquals(7, $db->query('UPDATE "godbtest" SET ?s WHERE id=?', array($set, 7))->id());

        $expected = array(
            array(6, 1, 8, 'six'),
            array(7, 1, 9, null),
            array(5, 2, 7, 'five'),
            array(4, 3, 7, null),
            array(2, 6, 3, 'two'),
            array(3, 6, 3, 'three'),
            array(1, 10, 1, 'one'),
        );

        $sql = 'SELECT "id","desc","num","val" FROM "godbtest" ORDER BY "desc" ASC, "id" ASC';
        $actual = $db->query($sql)->numerics();

        $this->assertEquals($expected, $actual);
        $this->assertNull($actual[1][3]);
        $this->assertNull($actual[3][3]);
    }
}
