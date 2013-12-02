<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Exceptions;

/**
 * @covers go\DB\Exceptions\Exception
 */
final class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testBacktrace()
    {
        $params = array(
            '_adapter' => 'test',
        );
        try {
            $db = \go\DB\DB::create($params);
        } catch (\go\DB\Exceptions\Config $e) {
            $this->assertEquals(__FILE__, $e->getFile());
        }
    }
}
