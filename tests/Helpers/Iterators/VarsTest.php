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
 * @coversDefaultClass go\DB\Helpers\Iterators\Vars
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class VarsTest extends \PHPUnit_Framework_TestCase
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

        $iterator = new Iterators\Vars($connector, new Fetcher($connector, $cursor));
        $result = \iterator_to_array($iterator);
        $expected = array(
            1 => 'One',
            3 => 'Three',
            5 => 'Five',
            7 => 'Seven',
        );
        $this->assertEquals($expected, $result);

        $cursor->reset();
        $iterator = new Iterators\Vars($connector, new Fetcher($connector, $cursor), 'name');
        $result = \iterator_to_array($iterator);
        $expected = array(
            1 => 'One',
            3 => 'Three',
            5 => 'Five',
            7 => 'Seven',
        );
        $this->assertEquals($expected, $result);
    }
}
