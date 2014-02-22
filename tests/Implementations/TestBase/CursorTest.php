<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Implementations\TestBase;

use go\DB\Implementations\TestBase\Cursor as Cursor;

/**
 * @covers \go\DB\Implementations\TestBase\Cursor
 */
final class CursorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \go\DB\Implementations\TestBase\Cursor::fetchAssoc
     */
    public function testAssoc()
    {
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
     * @covers \go\DB\Implementations\TestBase\Cursor::fetchRow
     */
    public function testRow()
    {
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
     * @covers \go\DB\Implementations\TestBase\Cursor::fetchObject
     */
    public function testObject()
    {
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
     * @covers \go\DB\Implementations\TestBase\Cursor::reset
     */
    public function testReset()
    {
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
     * @covers \go\DB\Implementations\TestBase\Cursor::getNumRows
     */
    public function testGetNumRows()
    {
        $data = array(
            array('a' => 1, 'b' => 2),
            array('a' => 3, 'b' => 4),
        );
        $cursor = new Cursor($data);
        $this->assertEquals(2, $cursor->getNumRows());
    }
}
