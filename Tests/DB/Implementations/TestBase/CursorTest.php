<?php
/**
 * Тестирование "курсора" тестовой базы
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB\Implementations\TestBase;

require_once(__DIR__.'/../../../Tests.php');

use go\DB\Implementations\TestBase\Cursor as Cursor;

/**
 * @covers \go\DB\Implementations\TestBase\Cursor
 */
final class CursorTest extends \go\Tests\DB\Base
{

    /**
     * @covers fetch_assoc
     */
    public function testAssoc() {
        $data = array(
            array('a' => 1, 'b' => 2),
            array('a' => 3, 'b' => 4),
        );
        $cursor = new Cursor($data);
        $this->assertEquals(array('a' => 1, 'b' => 2), $cursor->fetchAssoc());
        $this->assertEquals(array('a' => 3, 'b' => 4), $cursor->fetchAssoc());
        $this->assertFalse($cursor->fetchAssoc());
        $this->assertFalse($cursor->fetchAssoc());
    }

    /**
     * @covers fetch_row
     */
    public function testRow() {
        $data = array(
            array('a' => 1, 'b' => 2),
            array('a' => 3, 'b' => 4),
        );
        $cursor = new Cursor($data);
        $this->assertEquals(array(1, 2), $cursor->fetchRow());
        $this->assertEquals(array(3, 4), $cursor->fetchRow());
        $this->assertFalse($cursor->fetchRow());
        $this->assertFalse($cursor->fetchRow());
    }

    /**
     * @covers fetch_object
     */
    public function testObject() {
        $data = array(
            array('a' => 1, 'b' => 2),
            array('a' => 3, 'b' => 4),
        );
        $cursor = new Cursor($data);
        $this->assertEquals((object)array('a' => 1, 'b' => 2), $cursor->fetchObject());
        $this->assertEquals((object)array('a' => 3, 'b' => 4), $cursor->fetchObject());
        $this->assertFalse($cursor->fetchObject());
        $this->assertFalse($cursor->fetchObject());
    }

    /**
     * @covers reset
     */
    public function testReset() {
        $data = array(
            array('a' => 1, 'b' => 2),
            array('a' => 3, 'b' => 4),
        );
        $cursor = new Cursor($data);
        $this->assertEquals(array(1, 2), $cursor->fetchRow());
        $this->assertEquals(array(3, 4), $cursor->fetchRow());
        $this->assertFalse($cursor->fetchRow());
        $cursor->reset();
        $this->assertEquals(array(1, 2), $cursor->fetchRow());
        $this->assertEquals(array(3, 4), $cursor->fetchRow());
        $this->assertFalse($cursor->fetchRow());
    }

    /**
     * @covers getNumRows
     */
    public function testGetNumRows() {
        $data = array(
            array('a' => 1, 'b' => 2),
            array('a' => 3, 'b' => 4),
        );
        $cursor = new Cursor($data);
        $this->assertEquals(2, $cursor->getNumRows());
    }

}