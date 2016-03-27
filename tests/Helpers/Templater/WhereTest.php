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
        return [
            'plain' => [
                'WHERE ?w',
                [['x' => 1]],
                'WHERE `x`=1',
            ],
            'escape' => [
                'WHERE ?w',
                [['x' => 1, 'y' => 'qu"ot']],
                'WHERE `x`=1 AND `y`="qu\"ot"',
            ],
            'list' => [
                'WHERE ?where',
                [['x' => null, 'y' => [1, '2', 3]]],
                'WHERE `x` IS NULL AND `y` IN (1,"2",3)',
            ],
            'empty_list' => [
                'WHERE ?where',
                [['x' => null, 'y' => []]],
                'WHERE 1=0',
            ],
            'not_null' => [
                'WHERE ?where',
                [['one' => true, 'y' => '5', 'z' => 6]],
                'WHERE `one` IS NOT NULL AND `y`="5" AND `z`=6',
            ],
            'true' => [
                'WHERE ?w',
                [true],
                'WHERE 1=1',
            ],
            'empty' => [
                'WHERE ?w',
                [[]],
                'WHERE 1=1',
            ],
            'false' => [
                'WHERE ?w',
                [false],
                'WHERE 1=0',
            ],
            'operation1' => [
                'WHERE ?w',
                [['x' => 1, 'y' => ['op' => '<>', 'value' => 'xx']]],
                'WHERE `x`=1 AND `y`<>"xx"',
            ],
            'operation2' => [
                'WHERE ?w',
                [['x' => 2, 'y' => ['op' => '<=', 'col' => 'x']]],
                'WHERE `x`=2 AND `y`<=`x`',
            ],
            'col_value' => [
                'WHERE ?w',
                [['x' => 2, 'y' => ['op' => '=', 'col' => 'x', 'value' => 3]]],
                'WHERE `x`=2 AND `y`=`x`+3',
            ],
            'col_minus' => [
                'WHERE ?w',
                [['x' => 2, 'y' => ['op' => '<>', 'col' => 'z', 'value' => -5]]],
                'WHERE `x`=2 AND `y`<>`z`-5',
            ],
            'col_extended' => [
                'WHERE ?w',
                [
                    [
                        'col' => [
                            'op' => '<',
                            'db' => 'd',
                            'table' => 't',
                            'col' => 'c',
                            'value' => 3,
                            'func' => 'FUNC',
                        ],
                    ]
                ],
                'WHERE `col`<FUNC(`d`.`p_t`.`c`)+3',
                'p_',
            ],
        ];
    }
}
