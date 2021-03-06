<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Helpers\Debuggers;

use go\DB\Helpers\Debuggers\OutHtml;

/**
 * coversDefaultClass go\DB\Helpers\Debuggers\OutHtml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class OutHtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::__invoke
     */
    public function testTest()
    {
        $debugger = new OutHtml();
        ob_start();
        $debugger('"query"', 10, 'info');
        $log = ob_get_clean();
        $this->assertSame('<pre>&quot;query&quot;</pre>', trim($log));
    }
}
