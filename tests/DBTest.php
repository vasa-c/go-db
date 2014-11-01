<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB;

use go\DB\DB;
use go\DB\Helpers\Debuggers\Test;
use go\DB\Exceptions\Query as QueryException;
use go\DB\Implementations\Test as TestImp;

/**
 * @coversDefaultClass go\DB\DB
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class DBTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::create
     * @param array $params
     * @param string $adapter
     * @dataProvider providerCreate
     */
    public function testCreate($params, $adapter)
    {
        $db = DB::create($params, $adapter);
        $this->assertInstanceOf('go\DB\DB', $db);
    }

    /**
     * @return array
     */
    public function providerCreate()
    {
        return array(
            array( // the adapter is set separately from the parameters
                array(
                    'host' => 'localhost',
                ),
                'test',
            ),
            array( // the adapter is case insensitive
                array(
                    'host' => 'localhost',
                ),
                'Test',
            ),
            array( // the adapter is set inside the parameters
                array(
                    '_adapter' => 'test',
                    'host' => 'localhost',
                ),
                null,
            ),
            array( // the adapter in the parameters is case insensitive and replace a separate adapter
                array(
                    '_adapter' => 'Test',
                    'host' => 'localhost',
                ),
                'unknown',
            ),
        );
    }

    /**
     * @covers ::create
     * @param array $params
     * @param string $adapter
     * @dataProvider providerExceptionUnknownAdapter
     * @expectedException \go\DB\Exceptions\UnknownAdapter
     */
    public function testExceptionUnknownAdapter($params, $adapter)
    {
        DB::create($params, $adapter);
    }

    /**
     * @return array
     */
    public function providerExceptionUnknownAdapter()
    {
        return array(
            array(
                array(
                    'host' => 'localhost',
                ),
                'unknown',
            ),
            array(
                array(
                    '_adapter' => 'unknown',
                    'host'  => 'localhost',
                ),
                null,
            ),
            array(
                array(
                    '_adapter' => 'unknown',
                    'host' => 'localhost',
                ),
                'test',
            ),
        );
    }

    /**
     * @covers go\DB\DB::create
     * @expectedException \go\DB\Exceptions\ConfigSys
     */
    public function testExceptionConfigSys()
    {
        $params = array(
            '_adapter' => 'test',
            'host' => 'localhost',
            '_unknown' => 'error',
        );
        return DB::create($params);
    }

    /**
     * @covers ::create
     * @expectedException \go\DB\Exceptions\ConfigConnect
     */
    public function testExceptionConfigConnect()
    {
        $params = array(
            '_adapter' => 'test',
            'port' => 777,
        );
        return DB::create($params);
    }

    /**
     * @covers ::create
     */
    public function testExceptionConnect()
    {
        $params = array(
            '_adapter' => 'test',
            '_lazy' => true,
            'host' => 'notlocalhost',
            'port' => 777,
        );
        $db = DB::create($params);
        $this->setExpectedException('go\DB\Exceptions\Connect');
        $db->forcedConnect();
    }

    /**
     * go\DB\create()
     */
    public function testAliasCreate()
    {
        $db = \go\DB\create(array('host' => 'localhost'), 'test');
        $this->assertInstanceOf('go\DB\DB', $db);
        $this->setExpectedException('go\DB\Exceptions\UnknownAdapter');
        return \go\DB\create(array('host' => 'localhost'), 'unknown');
    }

    /**
     * @covers ::getAvailableAdapters
     */
    public function testGetAvailableAdapters()
    {
        $adapters = DB::getAvailableAdapters();
        $this->assertInternalType('array', $adapters);
        $this->assertContains('test', $adapters);
    }

    /**
     * @covers ::isConnected
     * @covers ::forcedConnect
     * @covers ::close
     */
    public function testConnectClose()
    {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
        );
        $db = DB::create($params);
        $this->assertFalse($db->isConnected());
        $db->forcedConnect();
        $this->assertTrue($db->isConnected());
        $engine = $db->getImplementationConnection(false);
        $this->assertFalse($engine->isClosed());
        $db->close(true); // soft
        $this->assertFalse($db->isConnected());
        $this->assertTrue($engine->isClosed());
        $db->plainQuery('INSERT');
        $this->assertTrue($db->isConnected());
        $engine = $db->getImplementationConnection(false);
        $this->assertFalse($engine->isClosed());
        $db->close(false); // not soft
        $this->assertFalse($db->isConnected());
        $this->setExpectedException('go\DB\Exceptions\Closed');
        $db->plainQuery('INSERT');
    }

    /**
     * @covers go\DB\DB::create
     */
    public function testNotLazyConnect()
    {
        $params = array(
            '_adapter' => 'test',
            '_lazy' => false,
            'host' => 'localhost',
        );
        $db = DB::create($params);
        $this->assertTrue($db->isConnected());
    }

    /**
     * @covers ::query
     */
    public function testQuery()
    {
        $params = array(
            '_adapter' => 'test',
            'host' => 'localhost',
        );
        $db = DB::create($params);
        $result = $db->query('SELECT * FROM `table` LIMIT ?i,?i', array(2, 2));
        $this->assertInstanceOf('go\DB\Result', $result);
        $result = $result->assoc();
        $expected = array(
            array('a' => 3, 'b' => 4, 'c' => 5),
            array('a' => 4, 'b' => 4, 'c' => 6),
        );
        $this->assertEquals($expected, $result);
        $this->setExpectedException('go\DB\Exceptions\Query'); // unknown table
        try {
            $db->query('SELECT * FROM `unknown` LIMIT ?i,?i', array(2, 2), 'assoc');
        } catch (QueryException $e) {
            $this->assertNotEmpty($e->getQuery());
            $this->assertNotEmpty($e->getError());
            throw $e;
        }
    }

    public function testMinus()
    {
        $params = array(
            '_adapter' => 'test',
            'host' => 'localhost',
        );
        $db = DB::create($params);
        $pattern = 'UPDATE `table` SET `x`=`x`-?i';
        $data = array(-1);
        $query = $db->makeQuery($pattern, $data);
        $this->assertEquals('UPDATE `table` SET `x`=`x`-(-1)', $query);
    }

    /**
     * @covers ::plainQuery
     */
    public function testPlainQuery()
    {
        $params = array(
            '_adapter' => 'test',
            'host' => 'localhost',
        );
        $db = DB::create($params);
        $result = $db->plainQuery('SELECT * FROM `table` LIMIT 2,2', 'assoc');
        $expected = array(
            array('a' => 3, 'b' => 4, 'c' => 5),
            array('a' => 4, 'b' => 4, 'c' => 6),
        );
        $this->assertEquals($expected, $result);
        $this->setExpectedException('go\DB\Exceptions\Query'); // unknown table
        $db->plainQuery('SELECT * FROM `unknown` LIMIT ?i,?i', array(2, 2), 'assoc');
    }

    /**
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $params = array(
            '_adapter' => 'test',
            'host' => 'localhost',
        );
        $db = DB::create($params);
        $result = $db('SELECT * FROM `table` LIMIT ?i,?i', array(2, 2), 'assoc');
        $expected = array(
            array('a' => 3, 'b' => 4, 'c' => 5),
            array('a' => 4, 'b' => 4, 'c' => 6),
        );
        $this->assertEquals($expected, $result);
        $this->setExpectedException('go\DB\Exceptions\Query'); // unknown table
        $db('SELECT * FROM `unknown` LIMIT ?i,?i', array(2, 2), 'assoc');
    }


    /**
     * @covers ::setPrefix
     * @covers ::getPrefix
     * @covers ::makeQuery
     */
    public function testPrefix()
    {
        $params = array(
            '_adapter' => 'test',
            'host' => 'localhost',
        );
        $db = DB::create($params);
        $prefix  = 'p_';
        $pattern = 'SELECT * FROM {table} LEFT JOIN ?t AS `t` ON ?c=?c';
        $data    = array('t', 'a', array('table', 'b'));
        $expectedN = 'SELECT * FROM `table` LEFT JOIN `t` AS `t` ON `a`=`table`.`b`';
        $expectedP = 'SELECT * FROM `p_table` LEFT JOIN `p_t` AS `t` ON `a`=`p_table`.`b`';
        $this->assertEmpty($db->getPrefix());
        $this->assertEquals($expectedN, $db->makeQuery($pattern, $data));
        $db->setPrefix($prefix);
        $this->assertEquals($prefix, $db->getPrefix());
        $this->assertEquals($expectedP, $db->makeQuery($pattern, $data));
        $db->setPrefix('');
        $this->assertEmpty($db->getPrefix());
        $this->assertEquals($expectedN, $db->makeQuery($pattern, $data));
        $this->assertEquals($expectedP, $db->makeQuery($pattern, $data, $prefix));
    }

    /**
     * @covers ::setDebug
     * @covers ::getDebug
     * @covers ::disableDebug
     * @covers ::query
     */
    public function testDebug()
    {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
        );
        $db = DB::create($params);
        $this->assertEmpty($db->getDebug());
        $debugger = new Test();
        $db->setDebug($debugger);
        $this->assertSame($debugger, $db->getDebug());
        $this->assertEmpty($debugger->getLastQuery());
        $db->query('UPDATE LIMIT ?i,?i', array(1, 2), 'ar');
        $this->assertEquals('UPDATE LIMIT 1,2', $debugger->getLastQuery());
        $db->query('UPDATE LIMIT ?i,?i', array(3, 4), 'ar');
        $this->assertEquals('UPDATE LIMIT 3,4', $debugger->getLastQuery());
        $db->disableDebug();
        $db->query('UPDATE LIMIT ?i,?i', array(5, 6), 'ar');
        $this->assertEquals('UPDATE LIMIT 3,4', $debugger->getLastQuery());
        $db->setDebug(true);
        $this->assertInternalType('object', $db->getDebug());
    }

    /**
     * @covers ::getImplementationConnection
     */
    public function testGetImplementationConnection()
    {
        $params = array(
            '_adapter' => 'test',
            'host' => 'localhost',
        );
        $db = DB::create($params);
        $this->assertEmpty($db->getImplementationConnection(false));
        $this->assertInstanceOf('go\DB\Implementations\TestBase\Engine', $db->getImplementationConnection(true));
        $this->assertInstanceOf('go\DB\Implementations\TestBase\Engine', $db->getImplementationConnection(false));
    }

    /**
     * @covers ::__clone
     */
    public function testClone()
    {
        $params = array(
            '_adapter' => 'test',
            'host' => 'localhost',
            '_prefix' => 'one_',
        );
        $db1 = DB::create($params);

        $db2 = clone $db1;
        $db2->setPrefix('two_');
        $this->assertFalse($db1->isConnected());
        $this->assertFalse($db2->isConnected());
        $query1 = $db1->makeQuery('SELECT * FROM {table}', null);
        $this->assertEquals('SELECT * FROM `one_table`', $query1);
        $this->assertTrue($db1->isConnected());
        $this->assertTrue($db2->isConnected());
        $engine1 = $db1->getImplementationConnection(false);
        $this->assertSame($engine1, $db2->getImplementationConnection(false));
        $query2 = $db2->makeQuery('SELECT * FROM {table}', null);
        $this->assertEquals('SELECT * FROM `two_table`', $query2);
        $this->assertFalse($db1->close(true));
        $this->assertTrue($db1->isConnected());
        $this->assertTrue($db2->isConnected());
        $this->assertFalse($db1->close(true));
        $this->assertTrue($db1->isConnected());
        $this->assertTrue($db2->isConnected());
        $this->assertFalse($engine1->isClosed());
        $this->assertTrue($db2->close(true));
        $this->assertFalse($db1->isConnected());
        $this->assertFalse($db2->isConnected());
        $this->assertTrue($engine1->isClosed());
        $db1->forcedConnect();
        $this->assertTrue($db1->isConnected());
        $this->assertTrue($db2->isConnected());
        $db1->close(true);
        $this->assertFalse($db1->isConnected());
        $this->assertFalse($db2->isConnected());
        $db1->forcedConnect();
        $engine2 = $db1->getImplementationConnection();
        $this->assertSame($engine2, $db2->getImplementationConnection(false));
        $this->assertNotSame($engine1, $engine2);
        $db2->forcedConnect();
        $db1->close(false);
        $this->assertFalse($db1->isConnected());
        $this->assertTrue($db2->isConnected());
    }

    /**
     * @covers ::__destruct
     */
    public function testDestruct()
    {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
            '_lazy'    => false,
        );
        $db1 = DB::create($params);
        $db2 = clone $db1;
        $engine = $db1->getImplementationConnection(false);
        $this->assertFalse($engine->isClosed());
        unset($db1);
        $this->assertFalse($engine->isClosed());
        unset($db2);
        $this->assertTrue($engine->isClosed());
    }

    public function testPreQueryLazy()
    {
        TestImp::resetLogs();
        $params = array(
            '_adapter' => 'test',
            'host' => 'localhost',
            '_prefix' => 'p_',
            '_pre' => array(
                'INSERT {tt}',
                array('INSERT ?t:table ?i:x', array('x' => 10, 'table' => 'qq')),
            ),
        );
        $db = DB::create($params);
        $db->preQuery('INSERT ?t ?i', array('tt', 11));
        $this->assertEmpty(TestImp::getLogs());
        $actual = $db->query('SELECT `a` FROM `table` LIMIT 2')->col();
        $this->assertEquals(array(1, 2), $actual);
        $expected = array(
            'connect',
            'query: INSERT `p_tt`',
            'query: INSERT `p_qq` 10',
            'query: INSERT `p_tt` 11',
            'query: SELECT `a` FROM `table` LIMIT 2',
            'freeCursor',
        );
        $this->assertEquals($expected, TestImp::getLogs());
        TestImp::resetLogs();
        $db->preQuery('INSERT');
        $this->assertEquals(array('query: INSERT'), TestImp::getLogs());
    }

    public function testPreQueryForce()
    {
        TestImp::resetLogs();
        $params = array(
            '_adapter' => 'test',
            'host' => 'localhost',
            '_prefix' => 'p_',
            '_lazy' => false,
            '_pre' => array(
                'INSERT {tt}',
                array('INSERT ?t:table ?i:x', array('x' => 10, 'table' => 'qq')),
            ),
        );
        $db = DB::create($params);
        $expected = array(
            'connect',
            'query: INSERT `p_tt`',
            'query: INSERT `p_qq` 10',
        );
        $this->assertEquals($expected, TestImp::getLogs());
        TestImp::resetLogs();
        $db->preQuery('INSERT');
        $this->assertEquals(array('query: INSERT'), TestImp::getLogs());
    }
}
