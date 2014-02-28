<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Helpers\Fetchers;

use go\DB\Helpers\Fetchers\Arr;

/**
 * @coversDefaultClass go\DB\Helpers\Fetchers\Arr
 */
class ArrTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $data = [
        ['id' => 1, 'a' => 5, 'b' => 10],
        ['id' => 2, 'a' => 15, 'b' => 5],
        ['id' => 3, 'a' => 25, 'b' => null],
    ];

    /**
     *
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
        $expected = [
            ['id' => 1, 'a' => 5, 'b' => 10],
            ['id' => 2, 'a' => 15, 'b' => 5],
            ['id' => 3, 'a' => 25, 'b' => null],
        ];
        $this->assertEquals($expected, $this->fetcher->assoc());
        $expected = [
            5 => ['id' => 1, 'a' => 5, 'b' => 10],
            15 => ['id' => 2, 'a' => 15, 'b' => 5],
            25 => ['id' => 3, 'a' => 25, 'b' => null],
        ];
        $this->assertEquals($expected, $this->fetcher->assoc('a'));
    }

    /**
     * @covers ::numerics
     */
    public function testNumerics()
    {
        $expected = [
            [1, 5, 10],
            [2, 15, 5],
            [3, 25, null],
        ];
        $this->assertEquals($expected, $this->fetcher->numerics());
        $expected = [
            5 => [1, 5, 10],
            15 => [2, 15, 5],
            25 => [3, 25, null],
        ];
        $this->assertEquals($expected, $this->fetcher->numerics(1));
    }

    /**
     * @covers ::objects
     */
    public function testObjects()
    {
        $expected = [
            (object)['id' => 1, 'a' => 5, 'b' => 10],
            (object)['id' => 2, 'a' => 15, 'b' => 5],
            (object)['id' => 3, 'a' => 25, 'b' => null],
        ];
        $this->assertEquals($expected, $this->fetcher->objects());
        $expected = [
            5 => (object)['id' => 1, 'a' => 5, 'b' => 10],
            15 => (object)['id' => 2, 'a' => 15, 'b' => 5],
            25 => (object)['id' => 3, 'a' => 25, 'b' => null],
        ];
        $this->assertEquals($expected, $this->fetcher->objects('a'));

    }

    /**
     * @covers ::col
     */
    public function testCol()
    {
        $expected = [1, 2, 3];
        $this->assertEquals($expected, $this->fetcher->col());
        $expected = [1, 2, 3];
        $this->assertEquals($expected, $this->fetcher->col('a'));
    }

    /**
     * @covers ::vars
     */
    public function testVars()
    {
        $expected = [1 => 5, 2 => 15, 3 => 25];
        $this->assertEquals($expected, $this->fetcher->vars());
        $expected = [1 => 5, 2 => 15, 3 => 25];
        $this->assertEquals($expected, $this->fetcher->vars('a'));
    }

    /**
     * @covers ::iassoc
     */
    public function testIassoc()
    {
        $expected = [
            ['id' => 1, 'a' => 5, 'b' => 10],
            ['id' => 2, 'a' => 15, 'b' => 5],
            ['id' => 3, 'a' => 25, 'b' => null],
        ];
        $actual = $this->fetcher->iassoc();
        $this->assertInstanceOf('Traversable', $actual);
        $this->assertEquals($expected, \iterator_to_array($actual));
        $expected = [
            5 => ['id' => 1, 'a' => 5, 'b' => 10],
            15 => ['id' => 2, 'a' => 15, 'b' => 5],
            25 => ['id' => 3, 'a' => 25, 'b' => null],
        ];
        $actual = $this->fetcher->iassoc('a');
        $this->assertEquals($expected, \iterator_to_array($actual));
    }

    /**
     * @covers ::inumerics
     */
    public function testINumerics()
    {
        $expected = [
            [1, 5, 10],
            [2, 15, 5],
            [3, 25, null],
        ];
        $actual = $this->fetcher->inumerics();
        $this->assertInstanceOf('Traversable', $actual);
        $this->assertEquals($expected, \iterator_to_array($actual));
        $expected = [
            5 => [1, 5, 10],
            15 => [2, 15, 5],
            25 => [3, 25, null],
        ];
        $actual = $this->fetcher->inumerics(1);
        $this->assertEquals($expected, \iterator_to_array($actual));
    }

    /**
     * @covers ::ivars
     */
    public function testIvars()
    {
        $expected = [1 => 5, 2 => 15, 3 => 25];
        $actual = $this->fetcher->ivars();
        $this->assertInstanceOf('Traversable', $actual);
        $this->assertEquals($expected, $this->fetcher->vars());
        $expected = [1 => 5, 2 => 15, 3 => 25];
        $actual = $this->fetcher->ivars('a');
        $this->assertEquals($expected, $this->fetcher->vars('a'));
    }

    /**
     * @covers ::icol
     */
    public function testICol()
    {
        $expected = [1, 2, 3];
        $actual = $this->fetcher->icol();
        $this->assertInstanceOf('Traversable', $actual);
        $this->assertEquals($expected, \iterator_to_array($actual));
        $expected = [1, 2, 3];
        $this->fetcher->icol('a');
        $this->assertEquals($expected, \iterator_to_array($actual));
    }

    /**
     * @covers ::row
     */
    public function testRow()
    {
        $expected = ['id' => 1, 'a' => 5, 'b' => 10];
        $this->assertEquals($expected, $this->fetcher->row());
        $fetcher = new Arr([]);
        $this->assertNull($fetcher->row());
    }

    /**
     * @covers ::numeric
     */
    public function testNumeric()
    {
        $expected = [1, 5, 10];
        $this->assertEquals($expected, $this->fetcher->numeric());
        $fetcher = new Arr([]);
        $this->assertNull($fetcher->numeric());
    }

    /**
     * @covers ::object
     */
    public function testObject()
    {
        $expected = (object)['id' => 1, 'a' => 5, 'b' => 10];
        $this->assertEquals($expected, $this->fetcher->object());
        $fetcher = new Arr([]);
        $this->assertNull($fetcher->object());
    }

    /**
     * @covers ::el
     */
    public function testEl()
    {
        $this->assertEquals(1, $this->fetcher->el());
        $fetcher = new Arr([]);
        $this->assertNull($fetcher->el());
    }

    /**
     * @covers ::bool
     */
    public function testBool()
    {
        $this->assertTrue($this->fetcher->bool());
        $fetcher = new Arr([['id' => 0]]);
        $this->assertFalse($fetcher->bool());
        $fetcher = new Arr([]);
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
        $expected = [
            5 => ['id' => 1, 'a' => 5, 'b' => 10],
            15 => ['id' => 2, 'a' => 15, 'b' => 5],
            25 => ['id' => 3, 'a' => 25, 'b' => null],
        ];
        $this->assertEquals($expected, $this->fetcher->fetch('assoc:a'));
    }

    /**
     * @covers ::__constructor
     */
    public function testConstruct()
    {
        $fetcher = new Arr($this->fetcher);
        $this->assertEquals($this->data, $fetcher->assoc());
        $this->setExpectedException('InvalidArgumentException');
        return new Arr(10);
    }
}
