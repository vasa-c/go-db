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
final class PrefixTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        $pattern = 'SELECT * FROM {table} AS `t` LEFT JOIN ?t ON ?t.?c=?c WHERE `id`=?';
        return array(
            array(
                $pattern,
                array('qwerty', 'qwerty', 'col', array('t', 'z'), 'value'),
                'SELECT * FROM `table` AS `t` LEFT JOIN `qwerty` ON `qwerty`.`col`=`t`.`z` WHERE `id`="value"',
                '',
            ),
            array(
                $pattern,
                array('qwerty', 'qwerty', 'col', array('t', 'z'), 'value'),
                'SELECT * FROM `prefix_table` AS `t` LEFT JOIN '.
                    '`prefix_qwerty` ON `prefix_qwerty`.`col`=`prefix_t`.`z` WHERE `id`="value"',
                'prefix_',
            ),
        );
    }
}
