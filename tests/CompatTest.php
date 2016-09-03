<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB;

use go\DB\Compat;
use go\DB\DB;

/**
 * coversDefaultClass go\DB\Compat
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class CompatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();
        Compat::setOpt('null', true);
        Compat::setOpt('types', true);
    }

    public function testSetOpt()
    {
        $db = DB::create(['host' => 'localhost'], 'test');
        $pattern = 'INSERT ?, ?, ?i, ?n, ?in, ?s, ?sn';
        $data = [2, null, null, null, null, ['x' => null], ['y' => null]];
        $expectedDef = 'INSERT 2, NULL, NULL, NULL, NULL, `x`=NULL, `y`=NULL';
        $this->assertSame($expectedDef, $db->makeQuery($pattern, $data));
        Compat::setOpt('null', false);
        Compat::setOpt('types', false);
        $expectedOld = 'INSERT "2", "", 0, NULL, NULL, `x`="", `y`=NULL';
        $this->assertSame($expectedOld, $db->makeQuery($pattern, $data));
        Compat::setOpt('null', true);
        Compat::setOpt('types', false);
        $this->assertSame('INSERT "2", NULL, NULL, NULL, NULL, `x`=NULL, `y`=NULL', $db->makeQuery($pattern, $data));
        Compat::setOpt('null', false);
        Compat::setOpt('types', true);
        $this->assertSame('INSERT 2, NULL, NULL, NULL, NULL, `x`=NULL, `y`=NULL', $db->makeQuery($pattern, $data));
        Compat::setOpt('null', true);
        Compat::setOpt('types', true);
        $this->assertSame($expectedDef, $db->makeQuery($pattern, $data));
    }

    public function testTableSet()
    {
        Compat::setOpt('null', false);
        $db = DB::create(['host' => 'localhost'], 'test');
        /** @var \go\DB\Implementations\TestBase\Engine $imp */
        $imp = $db->getImplementationConnection();
        $table = $db->getTable('t');
        $imp->resetLogs();
        $table->update(['x' => null, 'y' => 5]);
        $logs = $imp->getLogs();
        $this->assertArrayHasKey(0, $logs);
        $expected = 'query: UPDATE `t` SET `x`=NULL, `y`=5 WHERE 1=1';
        $this->assertSame($expected, $logs[0]);
        Compat::setOpt('null', true);
    }

    public function testSysParamCompat()
    {
        $db = DB::create(['host' => 'localhost'], 'test');
        $dbO = DB::create(['host' => 'localhost', '_compat' => ['null' => false, 'types' => false]], 'test');
        $this->assertSame('NULL', $db->makeQuery('?', [null]));
        $this->assertSame('""', $dbO->makeQuery('?', [null]));
        Compat::setOpt('null', false);
        Compat::setOpt('types', false);
        $this->assertSame('""', $db->makeQuery('?', [null]));
        $this->assertSame('""', $dbO->makeQuery('?', [null]));
        Compat::setOpt('null', true);
        Compat::setOpt('types', true);
        $this->assertSame('NULL', $db->makeQuery('?', [null]));
        $this->assertSame('""', $dbO->makeQuery('?', [null]));
    }
}
