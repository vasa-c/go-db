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
final class WhereTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        return array(
            array(
                'WHERE ?w',
                array(array('x' => 1)),
                'WHERE `x`=1',
            ),
            array(
                'WHERE ?w',
                array(array('x' => 1, 'y' => 'qu"ot')),
                'WHERE `x`=1 AND `y`="qu\"ot"',
            ),
            array(
                'WHERE ?where',
                array(array('x' => null, 'y' => array(1, '2', 3))),
                'WHERE `x` IS NULL AND `y` IN (1,"2",3)',
            ),
            array(
                'WHERE ?where',
                array(array('one' => true, 'y' => '5', 'z' => 6)),
                'WHERE `one` IS NOT NULL AND `y`="5" AND `z`=6',
            ),
            array(
                'WHERE ?w',
                array(true),
                'WHERE 1=1',
            ),
            array(
                'WHERE ?w',
                array(array()),
                'WHERE 1=1',
            ),
            array(
                'WHERE ?w',
                array(false),
                'WHERE 1=0',
            ),
            array(
                'WHERE ?w',
                array(array('x' => 1, 'y' => array('op' => '<>', 'value' => 'xx'))),
                'WHERE `x`=1 AND `y`<>"xx"',
            ),
            array(
                'WHERE ?w',
                array(array('x' => 2, 'y' => array('op' => '<=', 'col' => 'x'))),
                'WHERE `x`=2 AND `y`<=`x`',
            ),
            array(
                'WHERE ?w',
                array(array('x' => 2, 'y' => array('op' => '=', 'col' => 'x', 'value' => 3))),
                'WHERE `x`=2 AND `y`=`x`+3',
            ),
            array(
                'WHERE ?w',
                array(array('x' => 2, 'y' => array('op' => '<>', 'col' => 'z', 'value' => -5))),
                'WHERE `x`=2 AND `y`<>`z`-5',
            ),
        );
    }
}
