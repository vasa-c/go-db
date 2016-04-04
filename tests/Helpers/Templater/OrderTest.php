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
        return [
            'single' => [
                'ORDER BY ?o',
                ['id'],
                'ORDER BY `id` ASC',
            ],
            'list' => [
                'ORDER BY ?order;',
                [['a', 'b', 'c']],
                'ORDER BY `a` ASC,`b` ASC,`c` ASC',
            ],
            'asc_desc' => [
                'ORDER BY ?order',
                [['a' => false, 'b' => true, 'c' => false]],
                'ORDER BY `a` DESC,`b` ASC,`c` DESC',
            ],
            'table' => [
                'ORDER BY ?order',
                [[['d', 't', 'a'], 'b' => false]],
                'ORDER BY `d`.`p_t`.`a` ASC,`b` DESC',
                'p_',
            ],
            'col_extend' => [
                'ORDER BY ?order',
                [[['d', 't', 'a'], 'b' => false]],
                'ORDER BY `d`.`p_t`.`a` ASC,`b` DESC',
                'p_',
            ],
        ];
    }
}
