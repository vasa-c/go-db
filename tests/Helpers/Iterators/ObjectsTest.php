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
 * @cov@coversDefaultClassers go\DB\Helpers\Iterators\Objects
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class ObjectsTest extends \PHPUnit_Framework_TestCase
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

        $iterator = new Iterators\Objects($connector, new Fetcher($connector, $cursor));
        $result = \iterator_to_array($iterator);
        $expected = array(
            (object)array('id' => 1, 'name' => 'One'),
            (object)array('id' => 3, 'name' => 'Three'),
            (object)array('id' => 5, 'name' => 'Five'),
            (object)array('id' => 7, 'name' => 'Seven'),
        );
        $this->assertEquals($expected, $result);

        $cursor->reset();
        $iterator = new Iterators\Objects($connector, new Fetcher($connector, $cursor), 'id');
        $result = \iterator_to_array($iterator);
        $expected = array(
            1 => (object)array('id' => 1, 'name' => 'One'),
            3 => (object)array('id' => 3, 'name' => 'Three'),
            5 => (object)array('id' => 5, 'name' => 'Five'),
            7 => (object)array('id' => 7, 'name' => 'Seven'),
        );
        $this->assertEquals($expected, $result);
    }
}
