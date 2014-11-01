<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Helpers\Fetchers;

use go\DB\Helpers\Fetchers\Arr;

/**
 * @coversDefaultClass go\DB\Helpers\Fetchers\Arr
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class ArrTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $data = array(
        array('id' => 1, 'a' => 5, 'b' => 10),
        array('id' => 2, 'a' => 15, 'b' => 5),
        array('id' => 3, 'a' => 25, 'b' => null),
    );

    /**
     * @var \go\DB\Helpers\Fetchers\Arr
     */
    private $fetcher;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->fetcher = new Arr($this->data);
    }

    /**
     * @covers ::assoc
     */
    public function testAssoc()
    {
        $expected = array(
            array('id' => 1, 'a' => 5, 'b' => 10),
            array('id' => 2, 'a' => 15, 'b' => 5),
            array('id' => 3, 'a' => 25, 'b' => null),
        );
        $this->assertEquals($expected, $this->fetcher->assoc());
        $expected = array(
            5 => array('id' => 1, 'a' => 5, 'b' => 10),
            15 => array('id' => 2, 'a' => 15, 'b' => 5),
            25 => array('id' => 3, 'a' => 25, 'b' => null),
        );
        $this->assertEquals($expected, $this->fetcher->assoc('a'));
    }

    /**
     * @covers ::numerics
     */
    public function testNumerics()
    {
        $expected = array(
            array(1, 5, 10),
            array(2, 15, 5),
            array(3, 25, null),
        );
        $this->assertEquals($expected, $this->fetcher->numerics());
        $expected = array(
            5 => array(1, 5, 10),
            15 => array(2, 15, 5),
            25 => array(3, 25, null),
        );
        $this->assertEquals($expected, $this->fetcher->numerics(1));
    }

    /**
     * @covers ::objects
     */
    public function testObjects()
    {
        $expected = array(
            (object)array('id' => 1, 'a' => 5, 'b' => 10),
            (object)array('id' => 2, 'a' => 15, 'b' => 5),
            (object)array('id' => 3, 'a' => 25, 'b' => null),
        );
        $this->assertEquals($expected, $this->fetcher->objects());
        $expected = array(
            5 => (object)array('id' => 1, 'a' => 5, 'b' => 10),
            15 => (object)array('id' => 2, 'a' => 15, 'b' => 5),
            25 => (object)array('id' => 3, 'a' => 25, 'b' => null),
        );
        $this->assertEquals($expected, $this->fetcher->objects('a'));

    }

    /**
     * @covers ::col
     */
    public function testCol()
    {
        $expected = array(1, 2, 3);
        $this->assertEquals($expected, $this->fetcher->col());
        $expected = array(1, 2, 3);
        $this->assertEquals($expected, $this->fetcher->col('a'));
    }

    /**
     * @covers ::vars
     */
    public function testVars()
    {
        $expected = array(1 => 5, 2 => 15, 3 => 25);
        $this->assertEquals($expected, $this->fetcher->vars());
        $expected = array(1 => 5, 2 => 15, 3 => 25);
        $this->assertEquals($expected, $this->fetcher->vars('a'));
    }

    /**
     * @covers ::iassoc
     */
    public function testIassoc()
    {
        $expected = array(
            array('id' => 1, 'a' => 5, 'b' => 10),
            array('id' => 2, 'a' => 15, 'b' => 5),
            array('id' => 3, 'a' => 25, 'b' => null),
        );
        $actual = $this->fetcher->iassoc();
        $this->assertInstanceOf('Traversable', $actual);
        $this->assertEquals($expected, \iterator_to_array($actual));
        $expected = array(
            5 => array('id' => 1, 'a' => 5, 'b' => 10),
            15 => array('id' => 2, 'a' => 15, 'b' => 5),
            25 => array('id' => 3, 'a' => 25, 'b' => null),
        );
        $actual = $this->fetcher->iassoc('a');
        $this->assertEquals($expected, \iterator_to_array($actual));
    }

    /**
     * @covers ::inumerics
     */
    public function testINumerics()
    {
        $expected = array(
            array(1, 5, 10),
            array(2, 15, 5),
            array(3, 25, null),
        );
        $actual = $this->fetcher->inumerics();
        $this->assertInstanceOf('Traversable', $actual);
        $this->assertEquals($expected, \iterator_to_array($actual));
        $expected = array(
            5 => array(1, 5, 10),
            15 => array(2, 15, 5),
            25 => array(3, 25, null),
        );
        $actual = $this->fetcher->inumerics(1);
        $this->assertEquals($expected, \iterator_to_array($actual));
    }

    /**
     * @covers ::ivars
     */
    public function testIvars()
    {
        $expected = array(1 => 5, 2 => 15, 3 => 25);
        $actual = $this->fetcher->ivars();
        $this->assertInstanceOf('Traversable', $actual);
        $this->assertEquals($expected, \iterator_to_array($actual));
        $expected = array(1 => 5, 2 => 15, 3 => 25);
        $actual = $this->fetcher->ivars('a');
        $this->assertEquals($expected, \iterator_to_array($actual));
    }

    /**
     * @covers ::icol
     */
    public function testICol()
    {
        $expected = array(1, 2, 3);
        $actual = $this->fetcher->icol();
        $this->assertInstanceOf('Traversable', $actual);
        $this->assertEquals($expected, \iterator_to_array($actual));
        $expected = array(1, 2, 3);
        $this->fetcher->icol('a');
        $this->assertEquals($expected, \iterator_to_array($actual));
    }

    /**
     * @covers ::row
     */
    public function testRow()
    {
        $expected = array('id' => 1, 'a' => 5, 'b' => 10);
        $this->assertEquals($expected, $this->fetcher->row());
        $fetcher = new Arr(array());
        $this->assertNull($fetcher->row());
    }

    /**
     * @covers ::numeric
     */
    public function testNumeric()
    {
        $expected = array(1, 5, 10);
        $this->assertEquals($expected, $this->fetcher->numeric());
        $fetcher = new Arr(array());
        $this->assertNull($fetcher->numeric());
    }

    /**
     * @covers ::object
     */
    public function testObject()
    {
        $expected = (object)array('id' => 1, 'a' => 5, 'b' => 10);
        $this->assertEquals($expected, $this->fetcher->object());
        $fetcher = new Arr(array());
        $this->assertNull($fetcher->object());
    }

    /**
     * @covers ::el
     */
    public function testEl()
    {
        $this->assertEquals(1, $this->fetcher->el());
        $fetcher = new Arr(array());
        $this->assertNull($fetcher->el());
    }

    /**
     * @covers ::bool
     */
    public function testBool()
    {
        $this->assertTrue($this->fetcher->bool());
        $fetcher = new Arr(array(array('id' => 0)));
        $this->assertFalse($fetcher->bool());
        $fetcher = new Arr(array());
        $this->assertNull($fetcher->bool());
    }

    /**
     * @covers ::num
     */
    public function testNum()
    {
        $this->assertEquals(3, $this->fetcher->num());
    }

    /**
     * @covers ::id
     */
    public function testId()
    {
        $this->assertNull($this->fetcher->id());
        $idfetcher = new Arr(null, 10);
        $this->assertEmpty($idfetcher->assoc());
        $this->assertSame(0, $idfetcher->num());
        $this->assertSame(10, $idfetcher->id());
    }

    /**
     * @covers ::ar
     */
    public function testAr()
    {
        $this->assertEquals(0, $this->fetcher->ar());
    }

    /**
     * @covers ::cursor
     */
    public function testCursor()
    {
        $this->assertEquals($this->data, $this->fetcher->cursor());
    }

    public function testIterator()
    {
        $this->assertEquals($this->data, \iterator_to_array($this->fetcher));
    }

    /**
     * @covers ::fetch
     */
    public function testFetch()
    {
        $expected = array(
            5 => array('id' => 1, 'a' => 5, 'b' => 10),
            15 => array('id' => 2, 'a' => 15, 'b' => 5),
            25 => array('id' => 3, 'a' => 25, 'b' => null),
        );
        $this->assertEquals($expected, $this->fetcher->fetch('assoc:a'));
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $fetcher = new Arr($this->fetcher);
        $this->assertEquals($this->data, $fetcher->assoc());
        $this->setExpectedException('InvalidArgumentException');
        return new Arr(10);
    }
}
