<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Helpers\Iterators;

use go\DB\Helpers\Iterators;
use go\DB\Helpers\Connector;
use go\DB\Implementations\TestBase\Cursor;

/**
 * @covers go\DB\Helpers\Iterators\Numerics
 */
class NumericsTest extends \PHPUnit_Framework_TestCase
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

        $iterator = new Iterators\Numerics($connector, $cursor);
        $result = \iterator_to_array($iterator);
        $expected = array(
            array(1, 'One'),
            array(3, 'Three'),
            array(5, 'Five'),
            array(7, 'Seven'),
        );
        $this->assertEquals($expected, $result);

        $iterator = new Iterators\Numerics($connector, $cursor, '1');
        $result = \iterator_to_array($iterator);
        $expected = array(
            'One' => array(1, 'One'),
            'Three' => array(3, 'Three'),
            'Five' => array(5, 'Five'),
            'Seven' => array(7, 'Seven'),
        );
        $this->assertEquals($expected, $result);
    }
}
