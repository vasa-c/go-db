<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Helpers\Debuggers;

use go\DB\Helpers\Debuggers\OutHtml;

/**
 * @covers go\DB\Helpers\Debuggers\OutHtml
 */
class OutHtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\DB\Helpers\Debuggers\OutConsole::__invoke
     */
    public function testTest()
    {
        $debugger = new OutHtml();
        \ob_start();
        $debugger('"query"', 10, 'info');
        $log = \ob_get_clean();
        $this->assertSame('<pre>&quot;query&quot;</pre>', \trim($log));
    }
}
