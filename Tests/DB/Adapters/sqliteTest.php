<?php
/**
 * Тестирование адаптера sqlite
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB\Adapters;

require_once(__DIR__.'/Base.php');

/**
 * @covers \go\DB\Adapters\sqlite
 */
class sqliteTest extends Base
{
    public function setUp() {
        $this->markTestSkipped();
    }

    public function testInsert() {
        $helper = $this->getHelper();
        $db = $helper->getDB('fill');
        $helper->updated();

        $pattern = 'INSERT INTO ?t (?c, ?c) VALUES (?,?)';
        $data    = array('test_table', 'name', 'number', 'test', 77);
        $this->assertEquals(6, $db->query($pattern, $data, 'id'));
        $data    = array('test_table', 'name', 'number', 'test2', 78);
        $this->assertEquals(7, $db->query($pattern, $data, 'id'));
        $this->assertEquals(7, $db->query('SELECT COUNT(*) FROM {test_table}')->el());
        $name = $db->query('SELECT ?c FROM {test_table} WHERE ?c=?i', array('name', 'id', 6), 'el');
        $this->assertEquals('test', $name);
    }
    public function testDrop() {
        
    }
}