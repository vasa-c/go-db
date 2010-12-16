<?php
/**
 * Тестирование "движка" тестовой базы
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB\Implementations\TestBase;

require_once(__DIR__.'/../../../Tests.php');

use go\DB\Implementations\TestBase\Engine as Engine;

/**
 * @covers \go\DB\Implementations\TestBase\Engine
 */
final class EngineTest extends \go\Tests\DB\Base
{
    /**
     * @covers query
     * @dataProvider providerSelect
     */
    public function testSelect($query, $expected) {
        $engine = new Engine();
        $cursor = $engine->query($query);
        $this->assertInstanceOf('go\DB\Implementations\TestBase\Cursor', $cursor);
        $result = array();
        while ($row = $cursor->fetchAssoc()) {
            $result[] = $row;
        }
        $this->assertEquals($expected, $result);
    }
    public function providerSelect() {
        return array(
            array(
                'SELECT * FROM `table`',
                array(
                    array('a' => 1, 'b' => 2, 'c' => 3),
                    array('a' => 2, 'b' => 3, 'c' => 4),
                    array('a' => 3, 'b' => 4, 'c' => 5),
                    array('a' => 4, 'b' => 4, 'c' => 6),
                    array('a' => 5, 'b' => 4, 'c' => 7),
                    array('a' => 6, 'b' => 4, 'c' => 8),
                ),
            ),
            array(
                'SELECT `a`,`c` FROM `table`',
                array(
                    array('a' => 1, 'c' => 3),
                    array('a' => 2, 'c' => 4),
                    array('a' => 3, 'c' => 5),
                    array('a' => 4, 'c' => 6),
                    array('a' => 5, 'c' => 7),
                    array('a' => 6, 'c' => 8),
                ),
            ),
            array(
                'SELECT * FROM `table` LIMIT 2,2',
                array(
                    array('a' => 3, 'b' => 4, 'c' => 5),
                    array('a' => 4, 'b' => 4, 'c' => 6),
                ),
            ),
            array(
                'SELECT `b` FROM `table` LIMIT 2',
                array(
                    array('b' => 2),
                    array('b' => 3),
                ),
            ),
            array(
                'SELECT `c`,`b` FROM `table` LIMIT 5,4',
                array(
                    array('c' => 8, 'b' => 4),
                ),
            ),
        );
    }

    /**
     * @covers query
     * @covers getInstertId
     */
    public function testInsert() {
        $engine = new Engine();
        $this->assertEquals(0, $engine->getInsertId());
        $engine->query('INSERT INTO `table`');
        $this->assertEquals(1, $engine->getInsertId());
        $this->assertEquals(1, $engine->getInsertId());
        $engine->query('INSERT INTO `table`');
        $this->assertEquals(2, $engine->getInsertId());
        $engine->query('UPDATE `table`');
        $this->assertEquals(2, $engine->getInsertId());
        $engine->query('INSERT INTO `table`');
        $this->assertEquals(3, $engine->getInsertId());
    }

    /**
     * @covers query
     * @covers getAffectedRows
     */
    public function testUpdate() {
        $engine = new Engine();
        $this->assertEquals(0, $engine->getAffectedRows());
        $engine->query('UPDATE `table`');
        $this->assertEquals(6, $engine->getAffectedRows());
        $engine->query('UPDATE `table` LIMIT 2');
        $this->assertEquals(2, $engine->getAffectedRows());
        $engine->query('UPDATE `table` LIMIT 2,3');
        $this->assertEquals(3, $engine->getAffectedRows());
        $engine->query('UPDATE `table` LIMIT 4,3');
        $this->assertEquals(2, $engine->getAffectedRows());
        $engine->query('UPDATE `table` LIMIT 40,3');
        $this->assertEquals(0, $engine->getAffectedRows());
        $engine->query('SELECT * FROM `table`');
        $this->assertEquals(0, $engine->getAffectedRows());
    }

    /**
     * @covers query
     * @covers getErrorCode
     */
    public function testErrorOperator() {
        $engine = new Engine();
        $this->assertFalse($engine->query('SLCT * FROM `table`'));
        $this->assertEquals(Engine::ERROR_OPERATOR, $engine->getErrorCode());
    }

    /**
     * @covers query
     * @covers getErrorCode
     */
    public function testErrorTable() {
        $engine = new Engine();
        $this->assertFalse($engine->query('SELECT * FROM `tablicco`'));
        $this->assertEquals(Engine::ERROR_TABLE, $engine->getErrorCode());
    }
    
    /**
     * @covers query
     * @covers getErrorCode
     */
    public function testErrorCol() {
        $engine = new Engine();
        $this->assertFalse($engine->query('SELECT `a`,`b`,`x` FROM `table`'));
        $this->assertEquals(Engine::ERROR_COL, $engine->getErrorCode());
    }

    /**
     * @covers query
     * @covers getErrorInfo
     * @covers getErrorCode
     */
    public function testErrorInfo() {
        $engine = new Engine();
        $this->assertEmpty($engine->getErrorInfo());
        $this->assertEmpty($engine->getErrorCode());

        $this->assertFalse($engine->query('ERROR'));
        $this->assertNotEmpty($engine->getErrorInfo());
        $this->assertNotEmpty($engine->getErrorCode());

        $this->assertNotEmpty($engine->getErrorInfo());
        $this->assertNotEmpty($engine->getErrorCode());

        $this->assertTrue($engine->query('INSERT'));
        $this->assertEmpty($engine->getErrorInfo());
        $this->assertEmpty($engine->getErrorCode());
    }

    /**
     * @covers close
     * @covers isClosed
     */
    public function testClose() {
        $engine = new Engine();
        $this->assertFalse($engine->isClosed());
        $engine->close();
        $this->assertTrue($engine->isClosed());
    }

}