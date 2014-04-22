<?php
/**
 * @package go\DB
 */

namespace go\Tests\DB\Real\Mysql;

use go\Tests\DB\Real\Base;

class IteratorsTest extends Base
{
    /**
     * {@inheritdoc}
     */
    protected $adapter = 'mysql';

    /**
     * {@inheritdoc}
     */
    protected $reqExt = 'mysqli';

    public function testIterators()
    {
        $db = $this->createDB(__DIR__.'/dump.sql');
        $sql = 'SELECT `id`,`num` FROM `godbtest` ORDER BY `id` ASC';
        $assoc = $db($sql)->assoc();
        $it = $db($sql)->iassoc();
        $this->assertSame($assoc, \iterator_to_array($it));
        $this->assertSame($db($sql)->numerics(), \iterator_to_array($db($sql)->inumerics()));
        $this->assertSame($assoc, \iterator_to_array($it));
        $this->assertSame($db($sql)->col(), \iterator_to_array($db($sql)->icol()));
        $this->assertSame($db($sql)->vars(), \iterator_to_array($db($sql)->ivars()));
        $this->assertSame($db($sql)->assoc(), \iterator_to_array($db($sql)));
        $this->assertCount(5, $db($sql)->iassoc());
        $this->assertCount(5, $db($sql));
        $this->assertSame($db($sql)->assoc('id'), \iterator_to_array($db($sql)->iassoc('id')));
    }
}
