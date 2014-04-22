<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Helpers\Iterators;

use go\DB\Helpers\Iterators;
use go\DB\Helpers\Connector;
use go\DB\Implementations\TestBase\Cursor;

/**
 * @coversDefaultClass go\DB\Helpers\Iterators\Col
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class ColTest extends \PHPUnit_Framework_TestCase
{
    public function testIterator()
    {
        $data = array(
            array('id' => 1, 'name' => 'One'),
            array('id' => 3, 'name' => 'Three'),
            array('id' => 5, 'name' => 'Five'),
            array('id' => 7, 'name' => 'Seven'),
        );
        $connector = new Connector('test', array('host' => 'localhost'));
        $connector->connect();
        $cursor = new Cursor($data);

        $iterator = new Iterators\Col($connector, $cursor);
        $result = \iterator_to_array($iterator);
        $expected = array(1, 3, 5, 7);
        $this->assertEquals($expected, $result);

        $iterator = new Iterators\Col($connector, $cursor, 'name');
        $result = \iterator_to_array($iterator);
        $expected = array(1, 3, 5, 7);
        $this->assertEquals($expected, $result);
    }
}
