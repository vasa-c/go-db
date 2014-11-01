<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Helpers;

use go\DB\Helpers\ParserPH;
use go\DB\Helpers\Config;

/**
 * @coversDefaultClass go\DB\Helpers\ParserPH
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class ParserPHTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerParse
     * @param string $placeholder
     * @param string $expectType
     * @param string $expectModifiers
     */
    public function testParse($placeholder, $expectType, $expectModifiers)
    {
        $modifiers = self::getModifiers();
        $len = \strlen($expectModifiers);
        for ($i = 0; $i < $len; $i++) {
            $modifiers[\substr($expectModifiers, $i, 1)] = true;
        }
        $parser = new ParserPH($placeholder);
        $this->assertEquals($expectType, $parser->getType());
        $this->assertEquals($modifiers, $parser->getModifiers());
    }

    /**
     * @return array
     */
    public function providerParse()
    {
        return array(
            array('', '', 'n'),
            array('l', 'l', 'n'),
            array('s', 's', 'n'),
            array('v', 'v', 'n'),
            array('t', 't', 'n'),
            array('c', 'c', 'n'),
            array('e', 'e', 'n'),
            array('q', 'q', 'n'),
            array('string', '', 'n'),
            array('scalar', '', 'n'),
            array('list', 'l', 'n'),
            array('set', 's', 'n'),
            array('values', 'v', 'n'),
            array('table', 't', 'n'),
            array('col', 'c', 'n'),
            array('escape', 'e', 'n'),
            array('query', 'q', 'n'),
            array('ln', 'l', 'n'),
            array('li', 'l', 'ni'),
            array('lni', 'l', 'ni'),
            array('values-null', 'v', 'n'),
            array('values-float', 'v', 'nf'),
            array('values-bool-null', 'v', 'bn'),
            array('i', '', 'ni'),
            array('ni', '', 'ni'),
            array('null', '', 'n'),
            array('null-bool', '', 'nb'),
            array('cols', 'xc', 'n'),
        );
    }

    /**
     * @param string $placeholder
     * @dataProvider providerUnknownPlaceholder
     * @expectedException \go\DB\Exceptions\UnknownPlaceholder
     */
    public function testUnknownPlaceholder($placeholder)
    {
        return new ParserPH($placeholder);
    }

    /**
     * @return array
     */
    public function providerUnknownPlaceholder()
    {
        return array(
            array('u'),
            array('unknown'),
            array('vu'),
            array('values-unknown'),
        );
    }

    /**
     * @return array
     */
    private static function getModifiers()
    {
        if (!self::$modifiers) {
            $config = Config::get('placeholders');
            $modifiers = $config['modifiers'];
            self::$modifiers = array();
            foreach ($modifiers as $modifier) {
                self::$modifiers[$modifier] = false;
            }
        }
        return self::$modifiers;
    }

    /**
     * @var array
     */
    private static $modifiers;
}
