<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Exceptions;

/**
 * @coversDefaultClass go\DB\Exceptions\Runtime
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class RuntimeTest extends \PHPUnit_Framework_TestCase
{
    public function testBacktraceLogic()
    {
        $params = array(
            '_adapter' => 'test',
            'host' => 'invalid',
        );
        try {
            $db = \go\DB\DB::create($params);
            $line = __LINE__ + 1;
            $db->forcedConnect();
            $this->fail('not thrown');
        } catch (\go\DB\Exceptions\Connect $e) {
            $this->assertEquals(__FILE__, $e->getFile());
            $this->assertEquals($line, $e->getLine());
        }
    }
}
