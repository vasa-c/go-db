<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Helpers\Templater;

use go\DB\Helpers\Connector;
use go\DB\Helpers\Templater;

/**
 * coversDefaultClass go\DB\Helpers\Templater
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
abstract class BaseTemplater extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a templater instance
     *
     * @param string $pattern
     * @param array $data
     * @param string $prefix
     * @return \go\DB\Helpers\Templater
     */
    protected function createTemplater($pattern, $data, $prefix = null)
    {
        $connector = new Connector('test', array('host' => 'localhost'));
        $connector->connect();
        return new Templater($connector, $pattern, $data, $prefix);
    }
}
