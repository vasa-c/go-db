<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Implementations;

/**
 * @covers go\DB\Implementations\Base
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\DB\Implementations\Base::getImplementationForAdapter
     */
    public function testCreate()
    {
        $imp = \go\DB\Implementations\Base::getImplementationForAdapter('test');
        $this->assertInstanceOf('go\DB\Implementations\Base', $imp);
        $this->assertSame($imp, \go\DB\Implementations\Base::getImplementationForAdapter('test'));
    }

    /**
     * @covers go\DB\Implementations\Base::checkParams
     * @dataProvider providerCheckParams
     */
    public function testCheckParams($params, $expected)
    {
        $imp = \go\DB\Implementations\Base::getImplementationForAdapter('test');
        $this->assertEquals($expected, $imp->checkParams($params));
    }

    /**
     * @return array
     */
    public function providerCheckParams()
    {
        return array(
            array(
                array('host' => 'localhost'),
                array('host' => 'localhost', 'port' => 777),
            ),
            array(
                array('host' => 'localhost', 'port' => 555),
                array('host' => 'localhost', 'port' => 555),
            ),
            array(
                array('nothost' => 'localhost', 'port' => 555),
                false,
            ),
        );
    }

    /**
     * @covers go\DB\Implementations\Base::connect()
     */
    public function testConnect()
    {
        $implementation = \go\DB\Implementations\Base::getImplementationForAdapter('test');
        $params = array('host' => 'localhost');
        $connection = $implementation->connect($params, $errorInfo, $errorCode);
        $this->assertInstanceOf('go\DB\Implementations\TestBase\Engine', $connection);
        $params = array('host' => 'notlocalhost');
        $connection = $implementation->connect($params, $errorInfo, $errorCode);
        $this->assertFalse($connection);
        $this->assertEquals(\go\DB\Implementations\TestBase\Engine::ERROR_CONNECT, $errorCode);
    }

    /**
     * @covers go\DB\Implementations\Base::close()
     */
    public function testClose()
    {
        $this->createImp();
        $this->assertFalse($this->connection->isClosed());
        $this->implementation->close($this->connection);
        $this->assertTrue($this->connection->isClosed());
    }

    /**
     * @covers go\DB\Implementations\Base::query
     * @covers go\DB\Implementations\Base::isCursor
     * @covers go\DB\Implementations\Base::getInsertId
     * @covers go\DB\Implementations\Base::getAffectedRows
     * @covers go\DB\Implementations\Base::getErrorInfo
     * @covers go\DB\Implementations\Base::getErrorCode
     * @covers go\DB\Implementations\Base::getNumRows
     * @covers go\DB\Implementations\Base::fetchRow
     * @covers go\DB\Implementations\Base::fetchAssoc
     * @covers go\DB\Implementations\Base::fetchObject
     * @covers go\DB\Implementations\Base::rewindCursor
     */
    public function testQuery()
    {
        $this->createImp();
        $imp  = $this->implementation;
        $conn = $this->connection;
        $cursor = $imp->query($conn, 'SELECT * FROM `table` LIMIT 2');
        $this->assertTrue($imp->isCursor($conn, $cursor));
        $this->assertEquals(2, $imp->getNumRows($conn, $cursor));
        $this->assertEquals(array('a' => 1, 'b' => 2, 'c' => 3), $imp->fetchAssoc($conn, $cursor));
        $this->assertEquals(array(2, 3, 4), $imp->fetchRow($conn, $cursor));
        $this->assertEquals(false, $imp->fetchObject($conn, $cursor));
        $imp->rewindCursor($conn, $cursor);
        $this->assertEquals((object)array('a' => 1, 'b' => 2, 'c' => 3), $imp->fetchObject($conn, $cursor));
        $cursor = $imp->query($conn, 'INSERT');
        $this->assertFalse($imp->isCursor($conn, $cursor));
        $id = $imp->getInsertId($conn);
        $imp->query($conn, 'INSERT');
        $this->assertEquals($id + 1, $imp->getInsertId($conn));
        $imp->query($conn, 'UPDATE LIMIT 2');
        $this->assertEquals(2, $imp->getAffectedRows($conn));
        $this->assertFalse($imp->query($conn, 'ERROR'));
        $this->assertNotEmpty($imp->getErrorInfo($conn));
        $this->assertEquals(\go\DB\Implementations\TestBase\Engine::ERROR_OPERATOR, $imp->getErrorCode($conn));
    }

    /**
     * @covers go\DB\Implementations\Base::escapeString
     * @covers go\DB\Implementations\Base::reprString
     * @covers go\DB\Implementations\Base::reprInt
     * @covers go\DB\Implementations\Base::reprBool
     * @covers go\DB\Implementations\Base::reprNULL
     * @covers go\DB\Implementations\Base::reprTable
     * @covers go\DB\Implementations\Base::reprCol
     * @covers go\DB\Implementations\Base::reprChainFields
     */
    public function testRepr()
    {
        $this->createImp();
        $imp  = $this->implementation;
        $conn = $this->connection;
        $this->assertEquals('qw\"er', $imp->escapeString($conn, 'qw"er'));
        $this->assertEquals('"qw\"er"', $imp->reprString($conn, 'qw"er'));
        $this->assertEquals('123', $imp->reprInt($conn, '123d'));
        $this->assertEquals('1', $imp->reprBool($conn, 34));
        $this->assertEquals('0', $imp->reprBool($conn, false));
        $this->assertEquals('NULL', $imp->reprNULL($conn));
        $this->assertEquals('`table`', $imp->reprTable($conn, 'table'));
        $this->assertEquals('`col`', $imp->reprCol($conn, 'col'));
        $this->assertEquals('`db`.`table`.`col`', $imp->reprChainFields($conn, array('db', 'table', 'col')));
    }

    private function createImp()
    {
        $params = array('host' => 'localhost');
        $this->implementation = \go\DB\Implementations\Base::getImplementationForAdapter('test');
        $this->connection = $this->implementation->connect($params);
    }

    /**
     * @var \go\DB\Implementations\Base
     */
    private $implementation;

    /**
     * @var \go\DB\Implementations\TestBase\Engine
     */
    private $connection;
}
