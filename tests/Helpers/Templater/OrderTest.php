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
final class OrderTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        return array(
            array(
                'ORDER BY ?o',
                array('id'),
                'ORDER BY `id`',
            ),
            array(
                'ORDER BY ?order;',
                array(array('a', 'b', 'c')),
                'ORDER BY `a` ASC,`b` ASC,`c` ASC',
            ),
            array(
                'ORDER BY ?order',
                array(array('a' => false, 'b' => true, 'c' => false)),
                'ORDER BY `a` DESC,`b` ASC,`c` DESC',
            ),
            array(
                'ORDER BY ?order',
                array(array(array('t', 'a'), 'b' => false)),
                'ORDER BY `t`.`a` ASC,`b` DESC',
            ),
        );
    }
}
