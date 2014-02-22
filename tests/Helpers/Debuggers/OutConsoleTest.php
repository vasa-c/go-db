<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Helpers\Debuggers;

use go\DB\Helpers\Debuggers\OutConsole;

/**
 * @covers go\DB\Helpers\Debuggers\OutConsole
 */
class OutConsoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\DB\Helpers\Debuggers\OutConsole::__invoke
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
