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
