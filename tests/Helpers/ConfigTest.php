<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Helpers;

use go\DB\Helpers\Config;

/**
 * @coversDefaultClass go\DB\Helpers\Config
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::get
     */
    public function testGet()
    {
        $fetch = Config::get('fetch');
        $this->assertInternalType('array', $fetch);
        $this->assertArrayHasKey('row', $fetch);
        $this->assertSame(true, $fetch['col']);
        $this->assertEquals($fetch, Config::get('fetch'));
        $this->setExpectedException('RuntimeException');
        Config::get('unknown');
    }
}
