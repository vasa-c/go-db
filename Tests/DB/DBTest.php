<?php
/**
 * Тестирование базового поведения класса DB с помощью адаптера test
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB;

require_once(__DIR__.'/../Tests.php');

/**
 * @covers \go\DB\DB
 */
final class DBTest extends \go\Tests\DB\Base
{
    /**
     * @covers create
     * @dataProvider providerCreate
     */
    public function testCreate($params, $adapter) {
        $db = \go\DB\DB::create($params, $adapter);
        $this->assertInstanceOf('go\DB\DB', $db);
    }
    public function providerCreate() {
        return array(
            array( // адаптер отдельно
                array(
                    'host' => 'localhost',
                ),
                'test',
            ),
            array( // адаптер регистронезависим
                array(
                    'host' => 'localhost',
                ),
                'Test',
            ),
            array( // адаптер в параметрах
                array(
                    '_adapter' => 'test',
                    'host'     => 'localhost',
                ),
                null,
            ),
            array( // адаптер в параметрах также регистронезависим и перекрывает указанный отдельно
                array(
                    '_adapter' => 'Test',
                    'host'     => 'localhost',
                ),
                'unknown',
            ),
        );
    }

    /**
     * @covers create
     * @dataProvider providerExceptionUnknownAdapter
     * @expectedException go\DB\Exceptions\UnknownAdapter
     */
    public function testExceptionUnknownAdapter($params, $adapter) {
        $db = \go\DB\DB::create($params, $adapter);
        $this->assertInstanceOf('go\DB\DB', $db);
    }
    public function providerExceptionUnknownAdapter() {
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
                    'host'     => 'localhost',
                ),
                null,
            ),
            array(
                array(
                    '_adapter' => 'unknown',
                    'host'     => 'localhost',
                ),
                'test',
            ),
        );
    }

    /**
     * @covers create
     * @expectedException \go\DB\Exceptions\ConfigSys
     */
    public function testExceptionConfigSys() {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
            '_unknown' => 'error',
        );
        $db = \go\DB\DB::create($params);
    }

    /**
     * @covers create
     * @expectedException \go\DB\Exceptions\ConfigConnect
     */
    public function testExceptionConfigConnect() {
        $params = array(
            '_adapter' => 'test',
            'port'     => 777,
        );
        $db = \go\DB\DB::create($params);
    }

    /**
     * @covers create
     */
    public function testExceptionConnect() {
        $params = array(
            '_adapter' => 'test',
            '_lazy'    => true,
            'host'     => 'notlocalhost',
            'port'     => 777,
        );
        $db = \go\DB\DB::create($params);

        $this->setExpectedException('go\DB\Exceptions\Connect');
        $db->forcedConnect();
    }

    /**
     * @covers \go\DB\create()
     */
    public function testAliasCreate() {
        $db = \go\DB\create(array('host' => 'localhost'), 'test');
        $this->assertInstanceOf('go\DB\DB', $db);

        $this->setExpectedException('go\DB\Exceptions\UnknownAdapter');
        $db = \go\DB\create(array('host' => 'localhost'), 'unknown');
    }

    /**
     * @covers getAvailableAdapters
     */
    public function testGetAvailableAdapters() {
        $adapters = \go\DB\DB::getAvailableAdapters();
        $this->assertInternalType('array', $adapters);
        $this->assertContains('test', $adapters);
    }

    /**
     * @covers isConnected
     * @covers forcedConnect
     * @covers close
     */
    public function testConnectClose() {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
        );
        $db = \go\DB\DB::create($params);

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
     * @covers create
     */
    public function testNotLazyConnect() {
        $params = array(
            '_adapter' => 'test',
            '_lazy'    => false,
            'host'     => 'localhost',
        );
        $db = \go\DB\DB::create($params);
        $this->assertTrue($db->isConnected());
    }

    /**
     * @covers query
     */
    public function testQuery() {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
        );
        $db = \go\DB\DB::create($params);

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
            $result = $db->query('SELECT * FROM `unknown` LIMIT ?i,?i', array(2, 2), 'assoc');
        } catch (\go\DB\Exceptions\Query $e) {
            $this->assertNotEmpty($e->getQuery());
            $this->assertNotEmpty($e->getError());
            throw $e;
        }
    }
    
    public function testMinus() {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
        );
        $db = \go\DB\DB::create($params);
        
        $pattern = 'UPDATE `table` SET `x`=`x`-?i';
        $data    = array(-1);
        $query   = $db->makeQuery($pattern, $data);
        
        $this->assertEquals('UPDATE `table` SET `x`=`x`-(-1)', $query);
    }

    /**
     * @covers plainQuery
     */
    public function testPlainQuery() {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
        );
        $db = \go\DB\DB::create($params);

        $result = $db->plainQuery('SELECT * FROM `table` LIMIT 2,2', 'assoc');
        $expected = array(
            array('a' => 3, 'b' => 4, 'c' => 5),
            array('a' => 4, 'b' => 4, 'c' => 6),
        );
        $this->assertEquals($expected, $result);

        $this->setExpectedException('go\DB\Exceptions\Query'); // unknown table
        $result = $db->plainQuery('SELECT * FROM `unknown` LIMIT ?i,?i', array(2, 2), 'assoc');
    }

    /**
     * @covers __invoke
     */
    public function testInvoke() {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
        );
        $db = \go\DB\DB::create($params);

        $result = $db('SELECT * FROM `table` LIMIT ?i,?i', array(2, 2), 'assoc');
        $expected = array(
            array('a' => 3, 'b' => 4, 'c' => 5),
            array('a' => 4, 'b' => 4, 'c' => 6),
        );
        $this->assertEquals($expected, $result);

        $this->setExpectedException('go\DB\Exceptions\Query'); // unknown table
        $result = $db('SELECT * FROM `unknown` LIMIT ?i,?i', array(2, 2), 'assoc');
    }


    /**
     * @covers setPrefix
     * @covers getPrefix
     * @covers makeQuery
     */
    public function testPrefix() {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
        );
        $db = \go\DB\DB::create($params);

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
     * @covers setDebug
     * @covers getDebug
     * @covers disableDebug
     * @covers query
     */
    public function testDebug() {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
        );
        $db = \go\DB\DB::create($params);

        $this->assertEmpty($db->getDebug());

        $debugger = new \go\DB\Helpers\Debuggers\Test();
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
     * @covers getImplementationConnection
     */
    public function testGetImplementationConnection() {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
        );
        $db = \go\DB\DB::create($params);

        $this->assertEmpty($db->getImplementationConnection(false));
        $this->assertInstanceOf('go\DB\Implementations\TestBase\Engine', $db->getImplementationConnection(true));
        $this->assertInstanceOf('go\DB\Implementations\TestBase\Engine', $db->getImplementationConnection(false));
    }

    /**
     * @covers clone
     */
    public function testClone() {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
            '_prefix'  => 'one_',
        );
        $db1 = \go\DB\DB::create($params);

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
     * @covers __destruct
     */
    public function testDestruct() {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
            '_lazy'    => false,
        );
        $db1 = \go\DB\DB::create($params);
        $db2 = clone $db1;

        $engine = $db1->getImplementationConnection(false);

        $this->assertFalse($engine->isClosed());
        unset($db1);
        $this->assertFalse($engine->isClosed());
        unset($db2);
        $this->assertTrue($engine->isClosed());
    }
}