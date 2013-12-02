<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Helpers\Debuggers;

use go\DB\Helpers\Debuggers\Test;

/**
 * @covers go\DB\Helpers\Debuggers\Test
 */
class TestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\DB\Helpers\Debuggers\Test::__invoke
     * @covers go\DB\Helpers\Debuggers\Test::getLastQuery
     * @covers go\DB\Helpers\Debuggers\Test::getLastDuration
     * @covers go\DB\Helpers\Debuggers\Test::getLastInfo
     */
    public function testTest()
    {
        $debugger = new Test();
        $debugger('query', 10, 'info');
        $this->assertSame('query', $debugger->getLastQuery());
        $this->assertSame(10, $debugger->getLastDuration());
        $this->assertSame('info', $debugger->getLastInfo());
        $debugger('nquery', 11, 'ninfo');
        $this->assertSame('nquery', $debugger->getLastQuery());
        $this->assertSame(11, $debugger->getLastDuration());
        $this->assertSame('ninfo', $debugger->getLastInfo());
    }
}
