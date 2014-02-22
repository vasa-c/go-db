<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Exceptions;

use go\DB\Exceptions\Query;

/**
 * @covers go\DB\Exceptions\Query
 */
final class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\DB\Exceptions\Query::__construct
     * @covers go\DB\Exceptions\Query::getQuery
     * @covers go\DB\Exceptions\Query::getError
     * @covers go\DB\Exceptions\Query::getErrorCode
     */
    public function testGetMethods()
    {
        try {
            throw new Query('This is query', 'Error! Error!', 1234);
        } catch (Query $e) {
            $this->assertEquals('This is query', $e->getQuery());
            $this->assertEquals('Error! Error!', $e->getError());
            $this->assertEquals(1234, $e->getErrorCode());
        }
    }
}
