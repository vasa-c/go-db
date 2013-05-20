<?php
/**
 * Query-исключение
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB\Exceptions;

require_once(__DIR__.'/../../Tests.php');

use go\DB\Exceptions\Query;

/**
 * @covers go\DB\Exceptions\Query
 */
final class QueryTest extends \go\Tests\DB\Base
{
    /**
     * @covers go\DB\Exceptions\Query::__construct
     * @covers go\DB\Exceptions\Query::getQuery
     * @covers go\DB\Exceptions\Query::getError
     * @covers go\DB\Exceptions\Query::getErrorCode
     */
    public function testGetMethods() {
        try {
            throw new Query('This is query', 'Error! Error!', 1234);
        } catch (Query $e) {
            $this->assertEquals('This is query', $e->getQuery());
            $this->assertEquals('Error! Error!', $e->getError());
            $this->assertEquals(1234, $e->getErrorCode());
        }
    }
}
