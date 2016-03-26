<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Helpers\Templater;

/**
 * coversDefaultClass go\DB\Helpers\Templater
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
abstract class Base extends BaseTemplater
{
    /**
     * @param string $pattern
     * @param array $data
     * @param string $expected
     * @param string $prefix [optional]
     * @dataProvider providerTemplater
     */
    public function testTemplater($pattern, $data, $expected, $prefix = null)
    {
        $templater = $this->createTemplater($pattern, $data, $prefix);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }

    /**
     * @return array
     */
    abstract public function providerTemplater();
}
