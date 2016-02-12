<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Fakes;

use go\DB\Fakes\FakeTable;

/**
 * coversDefaultClass go\DB\Fakes\Helpers\FakeTable
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class FakeTableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::insert
     */
    public function testInsert()
    {
        $data = [
            ['id' => 3, 'name' => 'Three', 'age' => 20],
            ['id' => 5, 'name' => 'Five', 'age' => 35],
        ];
        $defaults = [
            'id' => null,
            'name' => null,
            'age' => 18,
        ];
        $table = new FakeTable($data, $defaults, 'id', true);
        $this->assertSame(6, $table->insert(['name' => 'Six', 'age' => 40]));
        $this->assertSame(7, $table->insert(['name' => 'Seven']));
        $this->assertSame(9, $table->insert(['name' => 'Nine', 'id' => 9]));
        $this->assertSame(10, $table->insert(['name' => 'Ten', 'age' => 28]));
        $expected = [
            ['id' => 3, 'name' => 'Three', 'age' => 20],
            ['id' => 5, 'name' => 'Five', 'age' => 35],
            ['id' => 6, 'name' => 'Six', 'age' => 40],
            ['id' => 7, 'name' => 'Seven', 'age' => 18],
            ['id' => 9, 'name' => 'Nine', 'age' => 18],
            ['id' => 10, 'name' => 'Ten', 'age' => 28],
        ];
        $this->assertEquals($expected, $table->getData());
        $this->assertSame(10, $table->getLastIncrement());
        $this->assertSame(['INSERT 6', 'INSERT 7', 'INSERT 9', 'INSERT 10'], $table->getLogs());
    }

    /**
     * covers ::insert
     */
    public function testMultiInsert()
    {
        $data = [
            ['id' => 3, 'name' => 'Three', 'age' => 20],
            ['id' => 5, 'name' => 'Five', 'age' => 35],
        ];
        $defaults = [
            'id' => null,
            'name' => null,
            'age' => 18,
        ];
        $table = new FakeTable($data, $defaults, 'id', true);
        $sets = [
            ['name' => 'Six'],
            ['name' => 'Seven', 'age' => 30],
        ];
        $table->multiInsert($sets);
        $expected = [
            ['id' => 3, 'name' => 'Three', 'age' => 20],
            ['id' => 5, 'name' => 'Five', 'age' => 35],
            ['id' => 6, 'name' => 'Six', 'age' => 18],
            ['id' => 7, 'name' => 'Seven', 'age' => 30],
        ];
        $this->assertEquals($expected, $table->getData());
        $this->assertSame(['INSERT MULTI 2'], $table->getLogs());
    }

    /**
     * covers ::replace
     */
    public function testReplace()
    {
        $data = [
            ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
            ['a' => 5, 'b' => 6, 'c' => 7, 'd' => 8],
        ];
        $defaults = [
            'a' => null,
            'b' => null,
            'c' => 10,
            'd' => 11,
        ];
        $table = new FakeTable($data, $defaults, ['a', 'b']);
        $table->replace(['a' => 5, 'b' => 6, 'c' => 15]);
        $table->replace(['a' => 5, 'b' => 7, 'c' => 25]);
        $expected = [
            ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
            ['a' => 5, 'b' => 6, 'c' => 15, 'd' => 11],
            ['a' => 5, 'b' => 7, 'c' => 25, 'd' => 11],
        ];
        $this->assertEquals($expected, $table->getData());
        $this->assertSame(['REPLACE UPDATE', 'REPLACE INSERT'], $table->getLogs());
    }

    /**
     * covers ::replace
     */
    public function testMultiReplace()
    {
        $data = [
            ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
            ['a' => 5, 'b' => 6, 'c' => 7, 'd' => 8],
        ];
        $defaults = [
            'a' => null,
            'b' => null,
            'c' => 10,
            'd' => 11,
        ];
        $table = new FakeTable($data, $defaults, ['a', 'b']);
        $sets = [
            ['a' => 5, 'b' => 7, 'c' => 25],
            ['a' => 5, 'b' => 6, 'c' => 15],
        ];
        $table->multiReplace($sets);
        $expected = [
            ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
            ['a' => 5, 'b' => 6, 'c' => 15, 'd' => 11],
            ['a' => 5, 'b' => 7, 'c' => 25, 'd' => 11],
        ];
        $this->assertEquals($expected, $table->getData());
        $this->assertSame(['REPLACE MULTI 2'], $table->getLogs());
    }

    /**
     * covers ::update
     */
    public function testUpdate()
    {
        $data = [
            ['a' => 1, 'b' => 5, 'c' => 4],
            ['a' => 2, 'b' => 6, 'c' => 3],
            ['a' => 3, 'b' => 7, 'c' => 2],
            ['a' => 4, 'b' => 8, 'c' => 1],
        ];
        $table = new FakeTable($data);
        $this->assertSame(2, $table->update(['a' => 11], ['b' => [5, 7]]));
        $this->assertSame(0, $table->update(['a' => 11], ['b' => [7]]));
        $expected = [
            ['a' => 11, 'b' => 5, 'c' => 4],
            ['a' => 2, 'b' => 6, 'c' => 3],
            ['a' => 11, 'b' => 7, 'c' => 2],
            ['a' => 4, 'b' => 8, 'c' => 1],
        ];
        $this->assertEquals($expected, $table->getData());
        $this->assertSame(['UPDATE 2', 'UPDATE 0'], $table->getLogs());
    }

    public function testDuplicate()
    {
        $data = [
            ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
            ['a' => 5, 'b' => 6, 'c' => 7, 'd' => 8],
        ];
        $defaults = [
            'a' => null,
            'b' => null,
            'c' => 10,
            'd' => 11,
        ];
        $table = new FakeTable($data, $defaults, ['a', 'b']);
        $this->setExpectedException('go\DB\Exceptions\Query');
        $table->insert(['a' => 5, 'b' => 6, 'c' => 7]);
    }

    /**
     * covers ::select
     */
    public function testSelect()
    {
        $data = [
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 2, 'b' => 12, 'c' => 3],
            ['a' => 3, 'b' => 22, 'c' => 3],
            ['a' => 4, 'b' => 32, 'c' => 4],
            ['a' => 5, 'b' => 12, 'c' => 3],
            ['a' => 6, 'b' => 24, 'c' => 5],
            ['a' => 7, 'b' => 21, 'c' => 3],
            ['a' => 8, 'b' => 25, 'c' => 7],
            ['a' => 9, 'b' => 11, 'c' => 3],
        ];
        $table = new FakeTable($data);
        $cursor = $table->select(['b', 'a'], ['c' => 3], ['b' => true, 'a' => false], [1, 4]);
        $this->assertInstanceOf('go\DB\Fakes\FakeResult', $cursor);
        $expected = [
            ['a' => 9, 'b' => 11],
            ['a' => 5, 'b' => 12],
            ['a' => 2, 'b' => 12],
            ['a' => 7, 'b' => 21],
        ];
        $this->assertEquals($expected, $cursor->cursor());
        $this->assertSame(['SELECT 4'], $table->getLogs());
    }

    /**
     * covers ::select
     */
    public function testDelete()
    {
        $data = [
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 2, 'b' => 12, 'c' => 3],
            ['a' => 3, 'b' => 22, 'c' => 3],
            ['a' => 4, 'b' => 32, 'c' => 4],
            ['a' => 5, 'b' => 12, 'c' => 3],
            ['a' => 6, 'b' => 24, 'c' => 5],
            ['a' => 7, 'b' => 21, 'c' => 3],
            ['a' => 8, 'b' => 25, 'c' => 7],
            ['a' => 9, 'b' => 11, 'c' => 3],
        ];
        $table = new FakeTable($data);
        $this->assertSame(6, $table->delete(['c' => 3]));
        $expected = [
            ['a' => 4, 'b' => 32, 'c' => 4],
            ['a' => 6, 'b' => 24, 'c' => 5],
            ['a' => 8, 'b' => 25, 'c' => 7],
        ];
        $this->assertEquals($expected, $table->getData());
        $this->assertSame(['DELETE 6'], $table->getLogs());
    }

    /**
     * covers ::select
     */
    public function testTruncate()
    {
        $data = [
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 2, 'b' => 12, 'c' => 3],
            ['a' => 3, 'b' => 22, 'c' => 3],
        ];
        $table = new FakeTable($data, null, 'a', true);
        $table->truncate();
        $expected = [];
        $this->assertEquals($expected, $table->getData());
        $this->assertSame(0, $table->getLastIncrement());
        $this->assertSame(['TRUNCATE'], $table->getLogs());
    }

    /**
     * covers ::getCount
     */
    public function testCount()
    {
        $data = [
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 2, 'b' => 12, 'c' => 3],
            ['a' => 3, 'b' => null, 'c' => 3],
            ['a' => 4, 'b' => 32, 'c' => 4],
            ['a' => 5, 'b' => 12, 'c' => 3],
            ['a' => 6, 'b' => null, 'c' => 5],
            ['a' => 7, 'b' => 21, 'c' => 3],
            ['a' => 8, 'b' => null, 'c' => 7],
            ['a' => 9, 'b' => 11, 'c' => 3],
        ];
        $table = new FakeTable($data);
        $this->assertSame(9, $table->getCount(null, null));
        $this->assertSame(6, $table->getCount('b', null));
        $this->assertSame(6, $table->getCount(null, ['c' => 3]));
        $this->assertSame(5, $table->getCount('b', ['c' => 3]));
        $this->assertSame(['COUNT 9', 'COUNT 6', 'COUNT 6', 'COUNT 5'], $table->getLogs());
    }

    /**
     * covers ::begin
     * covers ::commit
     * covers ::rollback
     */
    public function testTransaction()
    {
        $data = [
            ['id' => 1, 't' => 'a'],
            ['id' => 2, 't' => 'b'],
        ];
        $table = new FakeTable($data, null, 'id', true);
        $table->begin();
        $table->insert(['t' => 'c']);
        $table->insert(['t' => 'd']);
        $table->begin();
        $table->insert(['t' => 'e']);
        $table->insert(['t' => 'f']);
        $table->delete(['t' => 'b']);
        $expected = [
            ['id' => 1, 't' => 'a'],
            ['id' => 3, 't' => 'c'],
            ['id' => 4, 't' => 'd'],
            ['id' => 5, 't' => 'e'],
            ['id' => 6, 't' => 'f'],
        ];
        $this->assertEquals($expected, $table->getData());
        $table->rollback();
        $expected = [
            ['id' => 1, 't' => 'a'],
            ['id' => 2, 't' => 'b'],
            ['id' => 3, 't' => 'c'],
            ['id' => 4, 't' => 'd'],
        ];
        $this->assertEquals($expected, $table->getData());
        $table->begin();
        $table->insert(['t' => 'g']);
        $table->insert(['t' => 'h']);
        $table->commit();
        $table->delete(['t' => 'c']);
        $table->commit();
        $table->rollback();
        $expected = [
            ['id' => 1, 't' => 'a'],
            ['id' => 2, 't' => 'b'],
            ['id' => 4, 't' => 'd'],
            ['id' => 5, 't' => 'g'],
            ['id' => 6, 't' => 'h'],
        ];
        $this->assertEquals($expected, $table->getData());
        $logs = [
            'BEGIN',
            'INSERT 3',
            'INSERT 4',
            'BEGIN',
            'INSERT 5',
            'INSERT 6',
            'DELETE 1',
            'ROLLBACK',
            'BEGIN',
            'INSERT 5',
            'INSERT 6',
            'COMMIT',
            'DELETE 1',
            'COMMIT',
            'ROLLBACK',
        ];
        $this->assertSame($logs, $table->getLogs());
    }
}
