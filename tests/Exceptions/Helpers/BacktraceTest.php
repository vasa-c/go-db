<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Exceptions\Helpers;

use go\DB\Exceptions\Helpers\Backtrace;

/**
 * @coversDefaultClass go\DB\Exceptions\Helpers\Backtrace
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getTrace
     * @covers ::getFile
     * @covers ::getLine
     */
    public function testBacktrace()
    {
        $realtrace = array(
            array(),
            array('class' => 'my\lib\Hz'),
            array('class' => 'my\lib\Mz'),
            array(),
            array('class' => 'my\lib\Hz', 'file' => 'cfile', 'line' => 10),
            array(),
            array('class' => 'Qwe'),
            array(),
        );
        $backtrace = new Backtrace($realtrace, 'my\lib');
        $expected = array(
            array('class' => 'my\lib\Hz', 'file' => 'cfile', 'line' => 10),
            array(),
            array('class' => 'Qwe'),
            array(),
        );
        $this->assertEquals($expected, $backtrace->getTrace());
        $this->assertEquals('cfile', $backtrace->getFile());
        $this->assertEquals(10, $backtrace->getLine());
    }
}
