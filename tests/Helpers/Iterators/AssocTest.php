<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Helpers\Iterators;

use go\DB\Helpers\Iterators;
use go\DB\Helpers\Connector;
use go\DB\Implementations\TestBase\Cursor;
use go\DB\Helpers\Fetchers\Cursor as Fetcher;

/**
 * @coversDefaultClass go\DB\Helpers\Iterators\Assoc
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class AssocTest extends \PHPUnit_Framework_TestCase
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

        $iterator = new Iterators\Assoc($connector, new Fetcher($connector, $cursor));
        $expected = array(
            array('id' => 1, 'name' => 'One'),
            array('id' => 3, 'name' => 'Three'),
            array('id' => 5, 'name' => 'Five'),
            array('id' => 7, 'name' => 'Seven'),
        );
        $this->assertEquals($expected, \iterator_to_array($iterator));
        $this->assertEquals($expected, \iterator_to_array($iterator));

        $cursor->reset();

        $iterator = new Iterators\Assoc($connector, new Fetcher($connector, $cursor), 'id');
        $expected = array(
            1 => array('id' => 1, 'name' => 'One'),
            3 => array('id' => 3, 'name' => 'Three'),
            5 => array('id' => 5, 'name' => 'Five'),
            7 => array('id' => 7, 'name' => 'Seven'),
        );
        $this->assertEquals($expected, \iterator_to_array($iterator));
    }
}
