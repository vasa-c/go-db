<?php
/**
 * Тестирование подключалки
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB\Helpers;

require_once(__DIR__.'/../../Tests.php');

use go\DB\Helpers\Connector as Connector;
use go\DB\Implementations\test as Implementation;

/**
 * @covers \go\DB\Helpers\Connector
 */
final class ConnectorTest extends \go\Tests\DB\Base
{
    /**
     * @covers __construct
     */
    public function testCreate() {
        $params         = array('host' => 'localhost');
        $implementation = new Implementation();
        $connector      = new Connector($implementation, $params);

        $this->setExpectedException('go\DB\Exceptions\ConfigConnect');
        $params         = array('nohost' => 'localhost');
        $implementation = new Implementation();
        $connector      = new Connector($implementation, $params);
    }

    /**
     * @covers connect
     */
    public function testConnect() {
        $params         = array('host' => 'localhost');
        $implementation = new Implementation();
        $connector      = new Connector($implementation, $params);

        $this->assertFalse($connector->isConnected());
        $this->assertTrue($connector->connect());
        $this->assertTrue($connector->isConnected());
        $this->assertFalse($connector->connect());
        $this->assertTrue($connector->isConnected());

        $params         = array('host' => 'unknownhost');
        $implementation = new Implementation();
        $connector      = new Connector($implementation, $params);
        $this->setExpectedException('go\DB\Exceptions\Connect');
        $connector->connect();
    }

    /**
     * @covers close
     * @covers isConnected
     */
    public function testClose() {
        $params         = array('host' => 'localhost');
        $implementation = new Implementation();
        $connector      = new Connector($implementation, $params);

        $this->assertEmpty($implementation->getConnection());
        $connector->connect();
        $engine = $implementation->getConnection();
        $this->assertInstanceOf('go\DB\Implementations\TestBase\Engine', $engine);

        $this->assertTrue($connector->isConnected());
        $this->assertFalse($engine->isClosed());

        $this->assertTrue($connector->close());
        $this->assertFalse($connector->isConnected());
        $this->assertTrue($engine->isClosed());

        $this->assertFalse($connector->close());
        $this->assertFalse($connector->isConnected());
        $this->assertTrue($engine->isClosed());
    }
}