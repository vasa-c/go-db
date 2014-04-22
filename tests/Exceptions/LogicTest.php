<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Exceptions;

/**
 * @coversDefaultClass go\DB\Exceptions\Logic
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
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
