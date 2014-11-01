<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Helpers;

use go\DB\Helpers\Connector;

/**
 * @coversDefaultClass go\DB\Helpers\Connector
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class ConnectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $params = array(
            'host' => 'localhost',
        );
        $connector = new Connector('test', $params);
        $this->assertInstanceOf('go\DB\Implementations\Base', $connector->getImplementation());
    }

    /**
     * @covers ::__construct
     * @expectedException \go\DB\Exceptions\ConfigConnect
     */
    public function testConstructInvalidConfig()
    {
        $params = array(
            'nohost' => 'localhost',
        );
        return new Connector('test', $params);
    }

    /**
     * @covers ::connect
     */
    public function testConnect()
    {
        $params = array(
            'host' => 'localhost',
        );
        $connector = new Connector('test', $params);
        $this->assertFalse($connector->isConnected());
        $this->assertSame(0, $connector->getCountConnections());
        $this->assertTrue($connector->connect());
        $this->assertTrue($connector->isConnected());
        $this->assertSame(1, $connector->getCountConnections());
        $this->assertFalse($connector->connect());
        $this->assertTrue($connector->isConnected());
        $this->assertSame(2, $connector->getCountConnections());
    }

    /**
     * @covers ::connect
     * @expectedException \go\DB\Exceptions\Connect
     */
    public function testConnectErrorConfig()
    {
        $params = array(
            'host' => 'notlocalhost',
        );
        $connector = new Connector('test', $params);
        $connector->connect();
    }

    /**
     * @covers ::close
     * @covers ::isConnected
     */
    public function testClose()
    {
        $params = array(
            'host' => 'localhost'
        );
        $connector = new Connector('test', $params);
        $this->assertFalse($connector->close());
        $this->assertEmpty($connector->getConnection());
        $connector->connect();
        $connector->connect();
        $engine = $connector->getConnection();
        $this->assertInstanceOf('\go\DB\Implementations\TestBase\Engine', $engine);
        $this->assertTrue($connector->isConnected());
        $this->assertEquals(2, $connector->getCountConnections());
        $this->assertFalse($engine->isClosed());
        $this->assertFalse($connector->close());
        $this->assertTrue($connector->isConnected());
        $this->assertEquals(1, $connector->getCountConnections());
        $this->assertFalse($engine->isClosed());
        $this->assertTrue($connector->close());
        $this->assertFalse($connector->isConnected());
        $this->assertEquals(0, $connector->getCountConnections());
        $this->assertTrue($engine->isClosed());
        $this->assertFalse($connector->close());
        $this->assertFalse($connector->isConnected());
        $this->assertEquals(0, $connector->getCountConnections());
        $this->assertTrue($engine->isClosed());
        $expected = array(
            'close',
        );
        $this->assertEquals($expected, $engine->getLogs());
    }

    /**
     * @covers ::__construct
     * @covers ::connect
     * @covers ::close
     * @covers ::isConnected
     * @covers ::getCountConnections
     * @covers ::addLink
     * @covers ::removeLink
     */
    public function testShare()
    {
        $params = array(
            'host' => 'localhost'
        );
        $connector = new Connector('test', $params);
        $connector->connect();
        $engine = $connector->getConnection();
        $connector->connect();
        $this->assertSame($engine, $connector->getConnection());
        $this->assertEquals(2, $connector->getCountConnections());
        $connector->addLink(false);
        $this->assertEquals(2, $connector->getCountConnections());
        $connector->addLink(true);
        $this->assertEquals(3, $connector->getCountConnections());
        $connector->removeLink();
        $connector->removeLink();
        $this->assertEquals(3, $connector->getCountConnections());
        $this->assertFalse($engine->isClosed());
        $connector->removeLink();
        $this->assertEquals(0, $connector->getCountConnections());
        $this->assertTrue($engine->isClosed());
        $expected = array(
            'close',
        );
        $this->assertEquals($expected, $engine->getLogs());
    }

    /**
     * @covers ::__destruct
     */
    public function testDestructor()
    {
        $params = array(
            'host' => 'localhost',
        );
        $connector = new Connector('test', $params);
        $connector->connect();
        $engine = $connector->getConnection();
        $connector->connect();
        $this->assertFalse($engine->isClosed());
        unset($connector);
        $this->assertTrue($engine->isClosed());
        $expected = array(
            'close',
        );
        $this->assertEquals($expected, $engine->getLogs());
    }
}
