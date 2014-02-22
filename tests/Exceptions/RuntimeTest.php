<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Exceptions;

/**
 * @covers go\DB\Exceptions\Runtime
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
