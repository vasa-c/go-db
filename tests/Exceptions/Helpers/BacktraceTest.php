<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Exceptions\Helpers;

use go\DB\Exceptions\Helpers\Backtrace;

/**
 * @covers go\DB\Exceptions\Helpers\Backtrace
 */
final class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\DB\Exceptions\Helpers\Backtrace::__construct
     * @covers go\DB\Exceptions\Helpers\Backtrace::getTrace
     * @covers go\DB\Exceptions\Helpers\Backtrace::getFile
     * @covers go\DB\Exceptions\Helpers\Backtrace::getLine
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
