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
    public function testCreate() {
        $this->markTestSkipped();
    }

    public function testExceptionUnknownAdapter() {
        $this->markTestSkipped();
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

    public function testAliasCreate() {
        $this->markTestSkipped();
    }

    public function testGetAvailableAdapters() {
        $this->markTestSkipped();
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