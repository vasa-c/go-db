<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Helpers;

use go\DB\Helpers\Config;

/**
 * @covers go\DB\Helpers\Config
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\DB\Helpers\Config::get
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
