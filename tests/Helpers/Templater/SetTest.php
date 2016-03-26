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
final class SetTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        $set = array(
            's' => 'str"ing',
            'd' => '3.5',
            'n' => null,
        );
        return array(
            array(
                'INSERT INTO `table` SET ?s',
                array($set),
                'INSERT INTO `table` SET `s`="str\"ing", `d`="3.5", `n`=NULL',
            ),
            array(
                'INSERT INTO `table` SET ?set-null',
                array($set),
                'INSERT INTO `table` SET `s`="str\"ing", `d`="3.5", `n`=NULL',
            ),
            array(
                'INSERT INTO `table` SET ?sin',
                array($set),
                'INSERT INTO `table` SET `s`=0, `d`=3, `n`=NULL',
            ),
            array(
                'UPDATE ?t SET ?s WHERE `id`=?i',
                array('test', array('x' => array('col' => 'x', 'value' => 1), 'y' => 2), 10),
                'UPDATE `test` SET `x`=`x`+1, `y`="2" WHERE `id`=10',
            ),
            array(
                'SET ?sn',
                array(
                    array(
                        'a' => array(),
                        'b' => array('col' => 'x'),
                        'c' => array('col' => 'x', 'value' => -2),
                        'd' => array('value' => -3)),
                ),
                'SET `a`=NULL, `b`=`x`, `c`=`x`-2, `d`="-3"',
            ),
        );
    }
}
