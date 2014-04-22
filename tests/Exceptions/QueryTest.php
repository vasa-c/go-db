<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Exceptions;

use go\DB\Exceptions\Query;

/**
 * @coversDefaultClass go\DB\Exceptions\Query
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getQuery
     * @covers ::getError
     * @covers ::getErrorCode
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
