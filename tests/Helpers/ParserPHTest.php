<?php
/**
 * @package go\DB
 * @subpakcage Tests
 * @author Oleg Grigoriev aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\DB\Helpers;

use go\DB\Helpers\ParserPH as ParserPH;

/**
 * @covers go\DB\Helpers\ParserPH
 */
class ParserPHTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider providerParse
     *
     * @param string $placeholder
     *        исходный плейсхолдер
     * @param string $expectedType
     *        его ожидаемый тип
     * @param string $expectedModifer
     *        список его ожидаемых модификаторов
     */
    public function testParse($placeholder, $expectType, $expectModifers)
    {
        $modifers = self::getModifers();
        $len = \strlen($expectModifers);
        for ($i = 0; $i < $len; $i++) {
            $modifers[\substr($expectModifers, $i, 1)] = true;
        }
        $parser = new ParserPH($placeholder);
        $this->assertEquals($expectType, $parser->getType());
        $this->assertEquals($modifers, $parser->getModifers());
    }

    /**
     * @return array
     */
    public function providerParse()
    {
        return array(
            array('', '', ''),
            array('l', 'l', ''),
            array('s', 's', ''),
            array('v', 'v', ''),
            array('t', 't', ''),
            array('c', 'c', ''),
            array('e', 'e', ''),
            array('q', 'q', ''),
            array('string', '', ''),
            array('scalar', '', ''),
            array('list', 'l', ''),
            array('set', 's', ''),
            array('values', 'v', ''),
            array('table', 't', ''),
            array('col', 'c', ''),
            array('escape', 'e', ''),
            array('query', 'q', ''),
            array('ln', 'l', 'n'),
            array('li', 'l', 'i'),
            array('lni', 'l', 'ni'),
            array('values-null', 'v', 'n'),
            array('values-float', 'v', 'f'),
            array('values-bool-null', 'v', 'bn'),
            array('i', '', 'i'),
            array('ni', '', 'ni'),
            array('null', '', 'n'),
            array('null-bool', '', 'nb'),
            array('cols', 'xc', ''),
        );
    }

    /**
     * @dataProvider providerUnknownPlaceholder
     * @expectedException \go\DB\Exceptions\UnknownPlaceholder
     */
    public function testUnknownPlaceholder($placeholder)
    {
        $parser = new ParserPH($placeholder);
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

    private static function getModifers()
    {
        if (!self::$modifers) {
            $config = \go\DB\Helpers\Config::get('placeholders');
            $modifers = $config['modifers'];
            self::$modifers = array();
            foreach ($modifers as $modifer) {
                self::$modifers[$modifer] = false;
            }
        }
        return self::$modifers;
    }

    private static $modifers;
}
