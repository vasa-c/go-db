<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Implementations\TestBase;

use go\DB\Implementations\TestBase\Cursor as Cursor;

/**
 * @coversDefaultClass \go\DB\Implementations\TestBase\Cursor
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class CursorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::fetchAssoc
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
     * @covers ::fetchRow
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
     * @covers ::fetchObject
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
     * @covers ::reset
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
     * @covers ::getNumRows
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
