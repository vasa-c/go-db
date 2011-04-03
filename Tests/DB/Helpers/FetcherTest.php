<?php
/**
 * Тестирование разборщика результата
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB\Helpers;

require_once(__DIR__.'/../../Tests.php');

use go\DB\Helpers\Fetcher as Fetcher;

/**
 * @covers \go\DB\Helpers\Fetcher
 */
final class FetcherTest extends \go\Tests\DB\Base
{

    /**
     * assoc, numerics, objects, col, vars
     * @dataProvider providerList
     */
    public function testList($query, $fetch, $expected) {
        $this->assertEquals($expected, $this->dbQuery($query, $fetch));
    }
    public function providerList() {
        $query = 'SELECT `b`,`a` FROM `table` LIMIT 1,3';
        return array(
            array(
                $query,
                'assoc',
                array(
                    array('b' => 3, 'a' => 2),
                    array('b' => 4, 'a' => 3),
                    array('b' => 4, 'a' => 4),
                ),
            ),
            array(
                $query,
                'numerics',
                array(
                    array(3, 2),
                    array(4, 3),
                    array(4, 4),
                ),
            ),
            array(
                $query,
                'objects',
                array(
                    (object)array('b' => 3, 'a' => 2),
                    (object)array('b' => 4, 'a' => 3),
                    (object)array('b' => 4, 'a' => 4),
                ),
            ),
            array(
                $query,
                'col',
                array(3, 4, 4),
            ),
            array(
                $query,
                'vars',
                array(
                    3 => 2,
                    4 => 4,
                ),
            ),
        );
    }

    /**
     * assoc, numerics, objects, col, vars
     * @dataProvider providerListKey
     */
    public function testListKey($query, $fetch, $expected) {
        $result = $this->dbQuery($query, $fetch);
        $this->assertEquals($expected, $result);
    }
    public function providerListKey() {
        $query = 'SELECT `b`,`a` FROM `table` LIMIT 1,3';
        return array(
            array(
                $query,
                'assoc:b',
                array(
                    3 => array('b' => 3, 'a' => 2),
                    4 => array('b' => 4, 'a' => 4),
                ),
            ),
            array(
                $query,
                'numerics:0',
                array(
                    3 => array(3, 2),
                    4 => array(4, 4),
                ),
            ),
            array(
                $query,
                'objects:b',
                array(
                    3 => (object)array('b' => 3, 'a' => 2),
                    4 => (object)array('b' => 4, 'a' => 4),
                ),
            ),
        );
    }

    /**
     * iassoc, inumerics, iobjects, icol, ivars
     * @dataProvider providerIterators
     */
    public function testIterators($query, $fetch, $expected) {
        $iter = $this->dbQuery($query, $fetch);
        $this->assertInstanceOf('Iterator', $iter);
        $result = array();
        foreach ($iter as $k => $v) {
            $result[$k] = $v;
        }
        $this->assertEquals($expected, $result);
    }
    public function providerIterators() {
        $query = 'SELECT `b`,`a` FROM `table` LIMIT 1,3';
        return array(
            array(
                $query,
                'iassoc',
                array(
                    array('b' => 3, 'a' => 2),
                    array('b' => 4, 'a' => 3),
                    array('b' => 4, 'a' => 4),
                ),
            ),
            array(
                $query,
                'inumerics',
                array(
                    array(3, 2),
                    array(4, 3),
                    array(4, 4),
                ),
            ),
            array(
                $query,
                'iobjects',
                array(
                    (object)array('b' => 3, 'a' => 2),
                    (object)array('b' => 4, 'a' => 3),
                    (object)array('b' => 4, 'a' => 4),
                ),
            ),
            array(
                $query,
                'icol',
                array(3, 4, 4),
            ),
            array(
                $query,
                'ivars',
                array(
                    3 => 2,
                    4 => 4,
                ),
            ),
        );
    }

    /**
     * iassoc, inumerics, iobjects, icol, ivars
     * @dataProvider providerIteratorsKey
     */
    public function testIteratorsKey($query, $fetch, $expected) {
        $iter = $this->dbQuery($query, $fetch);
        $this->assertInstanceOf('Iterator', $iter);
        $result = array();
        foreach ($iter as $k => $v) {
            $result[$k] = $v;
        }
        $this->assertEquals($expected, $result);
    }
    public function providerIteratorsKey() {
        $query = 'SELECT `b`,`a` FROM `table` LIMIT 1,3';
        return array(
            array(
                $query,
                'iassoc:b',
                array(
                    3 => array('b' => 3, 'a' => 2),
                    4 => array('b' => 4, 'a' => 4),
                ),
            ),
            array(
                $query,
                'inumerics:0',
                array(
                    3 => array(3, 2),
                    4 => array(4, 4),
                ),
            ),
            array(
                $query,
                'iobjects:b',
                array(
                    3 => (object)array('b' => 3, 'a' => 2),
                    4 => (object)array('b' => 4, 'a' => 4),
                ),
            ),
        );
    }


    /**
     * row, numeric, object, el, bool
     * @dataProvider providerRow
     */
    public function testRow($query, $fetch, $expected) {
        $this->assertEquals($expected, $this->dbQuery($query, $fetch));
    }
    public function providerRow() {
        $query = 'SELECT * FROM `table` LIMIT 1';
        return array(
            array(
                $query,
                'row',
                array('a' => 1, 'b' => 2, 'c' => 3),
            ),
            array(
                $query,
                'numeric',
                array(1, 2, 3),
            ),
            array(
                $query,
                'object',
                (object)array('a' => 1, 'b' => 2, 'c' => 3),
            ),
        );
    }


    /**
     * el, bool
     * @dataProvider providerEl
     */
    public function testEl($query, $fetch, $expected) {
        $this->assertEquals($expected, $this->dbQuery($query, $fetch));
    }
    public function providerEl() {
        $query = 'SELECT `a` FROM `table` LIMIT 1';
        return array(
            array(
                $query,
                'el',
                1
            ),
            array(
                $query,
                'bool',
                true
            ),
        );
    }

    /**
     * num
     */
    public function testNum() {
        $query = 'SELECT * FROM `table` LIMIT 2,3';
        $this->assertEquals(3, $this->dbQuery($query, 'num'));
    }

    /**
     * @dataProvider providerEmpty
     */
    public function testEmpty($fetch, $expected) {
        $query   = 'SELECT * FROM `table` LIMIT 10,10';
        $result  = $this->dbQuery($query, $fetch);
        if (is_object($result)) {
            $iterator = $result;
            $result = array();
            foreach ($iterator as $key => $value) {
                $result[$key] = $value;
            }
        }
        $this->assertSame($expected, $result);
    }
    public function providerEmpty() {
        return array(
            array('assoc', array()),
            array('numerics', array()),
            array('col', array()),
            array('objects', array()),
            array('vars', array()),
            array('iassoc', array()),
            array('inumerics', array()),
            array('icol', array()),
            array('iobjects', array()),
            array('ivars', array()),
            array('row', null),
            array('numeric', null),
            array('object', null),
            array('el', null),
            array('bool', null),
        );
    }

    public function testFetcherIterator() {
        $query   = 'SELECT * FROM `table` LIMIT 0,3';
        $fetcher = $this->createFetcher($query);
        $result  = array();
        foreach ($fetcher as $row) {
            $result[] = $row;
        }
        $expected = array(
            array('a' => 1, 'b' => 2, 'c' => 3),
            array('a' => 2, 'b' => 3, 'c' => 4),
            array('a' => 3, 'b' => 4, 'c' => 5),
        );
        $this->assertEquals($expected, $result);
    }


    public function testNoSelect() {
        $this->assertEquals(1, $this->dbQuery('INSERT', 'id'));
        $this->assertEquals(3, $this->dbQuery('UPDATE LIMIT 3,3', 'ar'));
        $this->assertInternalType('object', $this->dbQuery('SELECT * FROM `table`', 'cursor'));
    }

    /**
     * @dataProvider providerUnknownFetch
     * @expectedException \go\DB\Exceptions\UnknownFetch
     */
    public function testUnknownFetch($fetch) {
        $this->dbQuery('SELECT * FROM `table`', $fetch);
    }
    public function providerUnknownFetch() {
        return array(
            array('qwerty'),
        );
    }

    /**
     * @dataProvider providerUnexpectedFetch
     * @expectedException \go\DB\Exceptions\UnexpectedFetch
     */
    public function testUnexpectedFetch($fetch) {
        $this->dbQuery('INSERT', $fetch);
    }
    public function providerUnexpectedFetch() {
        $fetches  = array(
            'assoc', 'numerics', 'objects', 'col', 'vars', 'iassoc', 'inumerics',
            'iobjects', 'ivars', 'icol', 'row', 'numeric', 'object', 'el', 'bool', 'num',
        );
        $provider = array();
        foreach ($fetches as $fetch) {
            $provider[] = array($fetch);
        }
        return $provider;
    }

    public function testIteratorsListsDiff() {
        $query = 'SELECT `b`,`a` FROM `table` LIMIT 1,3';

        $result = array();
        foreach ($this->dbQuery($query, 'vars') as $k => $v) {
            $result[] = array($k, $v);
        }
        $expected = array(
            array(3, 2),
            array(4, 4),
        );
        $this->assertEquals($expected, $result);

        $result = array();
        foreach ($this->dbQuery($query, 'ivars') as $k => $v) {
            $result[] = array($k, $v);
        }
        $expected = array(
            array(3, 2),
            array(4, 3),
            array(4, 4),
        );
        $this->assertEquals($expected, $result);
    }

    /**
     * [fix]: если вторая колонка NULL, "vars" считал, что выборка идёт по одной
     */
    public function testVarsNull() {
        $query = 'SELECT `a`,`null` FROM `table` LIMIT 1,3';

        $expected = array(
            '2' => null,
            '3' => null,
            '4' => null,
        );

        $result = array();
        foreach ($this->dbQuery($query, 'vars') as $k => $v) {
            $result[$k] = $v;
        }
        $this->assertEquals($expected, $result);

        $result = array();
        foreach ($this->dbQuery($query, 'ivars') as $k => $v) {
            $result[$k] = $v;
        }
        $this->assertEquals($expected, $result);
    }

    private function dbQuery($query, $fetch) {
        $fetcher = $this->createFetcher($query);
        return $fetcher->fetch($fetch);
    }

    /**
     * @return \go\DB\Result
     */
    private function createFetcher($query) {
        $connector = new \go\DB\Helpers\Connector('test', array('host' => 'localhost'));
        $connector->connect();
        $cursor = $connector->getConnection()->query($query);
        return (new Fetcher($connector, $cursor));
    }
}