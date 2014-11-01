<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Exceptions;

use go\DB\DB;
use go\DB\Exceptions\Connect;

/**
 * @coversDefaultClass go\DB\Exceptions\Runtime
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class RuntimeTest extends \PHPUnit_Framework_TestCase
{
    public function testBacktraceLogic()
    {
        $params = array(
            '_adapter' => 'test',
            'host' => 'invalid',
        );
        $line = null;
        try {
            $db = DB::create($params);
            $line = __LINE__ + 1;
            $db->forcedConnect();
            $this->fail('not thrown');
        } catch (Connect $e) {
            $this->assertEquals(__FILE__, $e->getFile());
            $this->assertEquals($line, $e->getLine());
        }
    }
}
