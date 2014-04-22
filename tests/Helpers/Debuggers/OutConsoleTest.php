<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Helpers\Debuggers;

use go\DB\Helpers\Debuggers\OutConsole;

/**
 * @coversDefaultClass go\DB\Helpers\Debuggers\OutConsole
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class OutConsoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__invoke
     */
    public function testTest()
    {
        $debugger = new OutConsole();
        \ob_start();
        $debugger('"query"', 10, 'info');
        $log = \ob_get_clean();
        $this->assertSame('"query"', \trim($log));
    }
}
