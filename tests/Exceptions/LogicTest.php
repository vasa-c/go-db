<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Exceptions;

/**
 * @covers go\DB\Exceptions\Logic
 */
final class LogicTest extends \PHPUnit_Framework_TestCase
{
    public function testBacktraceLogic()
    {
        $params = array(
            '_adapter' => 'test',
        );
        try {
            $line = __LINE__ + 1;
            $db = \go\DB\DB::create($params);
            $this->fail('not thrown');
        } catch (\go\DB\Exceptions\Config $e) {
            $this->assertEquals(__FILE__, $e->getFile());
            $this->assertEquals($line, $e->getLine());
        }
    }
}
