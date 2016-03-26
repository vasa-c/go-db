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
final class FieldsTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        return array(
            array(
                'INSERT INTO ?t SET ?c=?i',
                array('table', 'col', 1),
                'INSERT INTO `table` SET `col`=1'
            ),
            array(
                'INSERT INTO ?table SET ?col=?i',
                array('table', array('t', 'col'), 1),
                'INSERT INTO `table` SET `t`.`col`=1'
            ),
            array(
                'SELECT ?cols FROM ?t',
                array(array('a', 'b', 'c', array('t', 'd')), 'table'),
                'SELECT `a`,`b`,`c`,`t`.`d` FROM `table`',
            ),
            array(
                'SELECT ?cols FROM ?t',
                array('single', 'table'),
                'SELECT `single` FROM `table`',
            ),
            array(
                'SELECT ?cols FROM ?t',
                array(array(), 'table'),
                'SELECT * FROM `table`',
            ),
            array(
                'SELECT ?cols FROM ?t',
                array(true, 'table'),
                'SELECT * FROM `table`',
            ),
            array(
                'SELECT ?cols FROM `t`',
                array(array(array('t', 'x'))),
                'SELECT `t`.`x` FROM `t`',
            ),
            array(
                'SELECT ?col,?col,?col,?c,?col FROM `t`',
                array(
                    'a',
                    array('db' => 'd', 'table' => 't', 'col' => 'c', 'func' => 'COUNT', 'as' => 'zz'),
                    array('col' => 'cc', 'as' => 'aa'),
                    array('value' => 3, 'func' => 'COUNT'),
                    array('value' => 's"s'),
                ),
                'SELECT `a`,COUNT(`d`.`t`.`c`) AS `zz`,`cc` AS `aa`,COUNT(3),"s\"s" FROM `t`',
            ),
            array(
                'SELECT ?cols FROM `t`',
                array(
                    array(
                        'a',
                        array('db' => 'd', 'table' => 't', 'col' => 'c', 'func' => 'COUNT', 'as' => 'zz'),
                        array('col' => 'cc', 'as' => 'aa'),
                        array('value' => 3, 'func' => 'COUNT'),
                        array('value' => 's"s'),
                    ),
                ),
                'SELECT `a`,COUNT(`d`.`t`.`c`) AS `zz`,`cc` AS `aa`,COUNT(3),"s\"s" FROM `t`',
            ),
            array(
                'SELECT ?cols FROM ?t',
                array(true, array('database', 'table')),
                'SELECT * FROM `database`.`table`'
            ),
        );
    }
}
