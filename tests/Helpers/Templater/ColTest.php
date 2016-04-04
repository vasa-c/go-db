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
final class ColTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        return [
            'string' => [
                'SELECT ?c',
                ['id'],
                'SELECT `id`',
            ],
            'list' => [
                'SELECT ?c',
                [
                    ['one', 'two', 'three', 'four']
                ],
                'SELECT `one`.`two`.`three`.`four`',
            ],
            'a_string' => [
                'SELECT ?c',
                [
                    [
                        'col' => 'id'
                    ],
                ],
                'SELECT `id`',
            ],
            'a_list' => [
                'SELECT ?c',
                [
                    [
                        'col' => ['a', 'b'],
                    ],
                ],
                'SELECT `a`.`b`',
            ],
            'a_merge' => [
                'SELECT ?c',
                [
                    [
                        'col' => ['a', 'b'],
                        'table' => 'c',
                        'db' => ['d', 'e'],
                    ],
                ],
                'SELECT `d`.`e`.`c`.`a`.`b`',
            ],
            'func' => [
                'SELECT ?c',
                [
                    [
                        'col' => ['a', 'b'],
                        'func' => 'SUM',
                    ],
                ],
                'SELECT SUM(`a`.`b`)',
            ],
            'value' => [
                'SELECT ?c',
                [
                    [
                        'col' => 'col',
                        'value' => 1,
                    ],
                ],
                'SELECT `col`+1',
            ],
            'as' => [
                'SELECT ?c',
                [
                    [
                        'col' => 'col',
                        'as' => 'id',
                    ],
                ],
                'SELECT `col` AS `id`',
            ],
            'mixed' => [
                'SELECT ?c',
                [
                    [
                        'db' => 'dbname',
                        'table' => 'test',
                        'col' => 'value',
                        'func' => 'COUNT',
                        'value' => -3,
                        'as' => 'q',
                    ],
                ],
                'SELECT COUNT(`dbname`.`test`.`value`)-3 AS `q`',
            ],
            'only_value' => [
                'SELECT ?c',
                [
                    [
                        'value' => 2,
                        'as' => 'two',
                    ],
                ],
                'SELECT 2 AS `two`',
            ],
            'prefix_list' => [
                'SELECT ?c',
                [
                    ['a', 'b', 'c']
                ],
                'SELECT `a`.`prefix_b`.`c`',
                'prefix_',
            ],
            'prefix_table' => [
                'SELECT ?c',
                [
                    [
                        'col' => 'a',
                        'table' => ['b', 'c'],
                        'db' => 'd',
                    ],
                ],
                'SELECT `d`.`b`.`prefix_c`.`a`',
                'prefix_',
            ],
            'int-string' => [
                'SELECT ?c',
                [
                    [
                        'col' => 'col',
                        'value' => '1zz',
                    ],
                ],
                'SELECT `col`+1',
            ],
            'int-only-value' => [
                'SELECT ?c',
                [
                    [
                        'value' => '1z"z',
                        'as' => 'x',
                    ],
                ],
                'SELECT "1z\"z" AS `x`',
            ],
            'func-only' => [
                'UPDATE `t` SET `x`=?col',
                [
                    [
                        'func' => 'NOW',
                    ],
                ],
                'UPDATE `t` SET `x`=NOW()',
            ],
            'func-value' => [
                'SELECT ?c FROM `t`',
                [
                    [
                        'func' => 'COUNT',
                        'value' => 2,
                        'as' => 'c',
                    ],
                ],
                'SELECT COUNT(2) AS `c` FROM `t`',
            ],
            'func-col-value' => [
                'SELECT ?c FROM `t`',
                [
                    [
                        'func' => 'COUNT',
                        'col' => 'id',
                        'value' => -2,
                        'as' => 'c',
                    ],
                ],
                'SELECT COUNT(`id`)-2 AS `c` FROM `t`',
            ],
            'func-no-args-value' => [
                'SELECT ?c FROM `t`',
                [
                    [
                        'func' => 'NOW',
                        'value' => 2,
                        'col' => null,
                        'as' => 'c',
                    ],
                ],
                'SELECT NOW()+2 AS `c` FROM `t`',
            ],
            'all' => [
                'SELECT ?c FROM `t`',
                [
                    [
                        'func' => 'COUNT',
                        'value' => 2,
                        'col' => true,
                        'as' => 'c',
                    ],
                ],
                'SELECT COUNT(*)+2 AS `c` FROM `t`',
            ],
        ];
    }
}
