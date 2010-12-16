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
        $this->assertType('array', $adapters);
        $this->assertContains('test', $adapters);
    }

    public function testConnectClose() {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
        );
        $db = \go\DB\DB::create($params);
        
        $this->assertFalse($db->isConnected());
        $db->forceConnect();
        $this->assertTrue($db->isConnected());
        $db->close(true); // safe
        $this->assertFalse($db->isConnected());
        $db->plainQuery('INSERT');
        $this->assertTrue($db->isConnected());

        $db->close(false); // not safe
        $this->assertFalse($db->isConnected());
        $this->setExpectedException('go\DB\Exceptions\Closed');
        $db->plainQuery('INSERT');
    }

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

        $result = $db->query('SELECT * FROM `table` WHERE LIMIT ?i,?i', array(2, 2));
        $this->assertInstanceOf('go\DB\Result', $result);
        $result = $result->assoc();
        $expected = array(
            array('a' => 3, 'b' => 4, 'c' => 5),
            array('a' => 4, 'b' => 4, 'c' => 6),
        );
        $this->assertEquals($expected, $result);

        $this->setExpectedException('go\DB\Exceptions\Query'); // unknown table
        $result = $db->query('SELECT * FROM `unknown` WHERE LIMIT ?i,?i', array(2, 2), 'assoc');
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

        $result = $db->query('SELECT * FROM `table` WHERE LIMIT 2,2', 'assoc');
        $expected = array(
            array('a' => 3, 'b' => 4, 'c' => 5),
            array('a' => 4, 'b' => 4, 'c' => 6),
        );
        $this->assertEquals($expected, $result);

        $this->setExpectedException('go\DB\Exceptions\Query'); // unknown table
        $result = $db->query('SELECT * FROM `unknown` WHERE LIMIT ?i,?i', array(2, 2), 'assoc');
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

        $result = $db('SELECT * FROM `table` WHERE LIMIT ?i,?i', array(2, 2), '');
        $expected = array(
            array('a' => 3, 'b' => 4, 'c' => 5),
            array('a' => 4, 'b' => 4, 'c' => 6),
        );
        $this->assertEquals($expected, $result);

        $this->setExpectedException('go\DB\Exceptions\Query'); // unknown table
        $result = $db('SELECT * FROM `unknown` WHERE LIMIT ?i,?i', array(2, 2), '');
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
        $this->assertEmpty($debugger->getQuery());

        $db->query('UPDATE LIMIT ?i,?i', array(1, 2), 'ar');
        $this->assertEquals('UPDATE LIMIT 1,2', $debugger->getQuery());

        $db->query('UPDATE LIMIT ?i,?i', array(3, 4), 'ar');
        $this->assertEquals('UPDATE LIMIT 3,4', $debugger->getQuery());
        
        $db->disableDebug();
        $db->query('UPDATE LIMIT ?i,?i', array(5, 6), 'ar');
        $this->assertEquals('UPDATE LIMIT 3,4', $debugger->getQuery());

        $db->setDebug(true);
        $this->assertType('object', $db->getDebug());
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

        $this->assertEmpty($db->getImplementationConnection());

        $db->forcedConnect();
        $this->assertInstanceOf('go\DB\Implementations\TestBase\Engine', $db->getImplementationConnection());
    }
}