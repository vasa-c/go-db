<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Helpers\Debuggers;

use go\DB\Helpers\Debuggers\Test;

/**
 * @coversDefaultClass go\DB\Helpers\Debuggers\Test
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class TestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__invoke
     * @covers ::getLastQuery
     * @covers ::getLastDuration
     * @covers ::getLastInfo
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
