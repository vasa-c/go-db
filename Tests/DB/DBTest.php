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

    public function testExceptionConfigSys() {
        $this->markTestSkipped();
    }

    public function testExceptionConfigConnect() {
        $this->markTestSkipped();
    }

    public function testExceptionConnect() {
        $this->markTestSkipped();
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

    public function testQuery() {
        $this->markTestSkipped();
    }

    public function testPlainQuery() {
        $this->markTestSkipped();
    }

    public function testInvoke() {
        $this->markTestSkipped();
    }

    public function testConnect() {
        $this->markTestSkipped();
    }

    public function testClose() {
        $this->markTestSkipped();
    }

    public function testPrefix() {
        $this->markTestSkipped();
    }

    public function testDebug() {
        $this->markTestSkipped();
    }

    public function testLazyConnection() {
        $this->markTestSkipped();
    }

    public function testGetImplementationConnection() {
        $this->markTestSkipped();
    }
}