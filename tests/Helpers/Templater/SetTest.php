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
        $set = [
            's' => 'str"ing',
            'd' => '3.5',
            'n' => null,
        ];
        return [
            'escape' => [
                'INSERT INTO `table` SET ?s',
                [$set],
                'INSERT INTO `table` SET `s`="str\"ing", `d`="3.5", `n`=NULL',
            ],
            'subset' => [
                'INSERT INTO `table` SET ?s[?string:s, ?int:d, ?null:n]',
                [$set],
                'INSERT INTO `table` SET `s`="str\"ing", `d`=3, `n`=NULL',
            ],
            'null' => [
                'INSERT INTO `table` SET ?set-null',
                [$set],
                'INSERT INTO `table` SET `s`="str\"ing", `d`="3.5", `n`=NULL',
            ],
            'int-null' => [
                'INSERT INTO `table` SET ?sin',
                [$set],
                'INSERT INTO `table` SET `s`=0, `d`=3, `n`=NULL',
            ],
            'int' => [
                'UPDATE ?t SET ?s WHERE `id`=?i',
                ['test', ['x' => ['col' => 'x', 'value' => 1], 'y' => 2], 10],
                'UPDATE `test` SET `x`=`x`+1, `y`=2 WHERE `id`=10',
            ],
            'extended' => [
                'SET ?s',
                [
                    [
                        'a' => [],
                        'b' => ['col' => 'x'],
                        'c' => ['col' => 'x', 'value' => -2],
                        'd' => ['value' => -3],
                    ],
                ],
                'SET `a`=NULL, `b`=`x`, `c`=`x`-2, `d`=(-3)',
            ],
            'extended_ext_col' => [
                'SET ?s',
                [
                    [
                        'cc' => [
                            'col' => ['a', 'b', 'c'],
                            'value' => '2x',
                            'func' => 'SUM'
                        ],
                        'cx' => [
                            'value' => 5,
                            'func' => 'SUM',
                        ],
                    ],
                ],
                'SET `cc`=SUM(`a`.`p_b`.`c`)+2, `cx`=SUM(5)',
                'p_',
            ],
            'int-vs-string' => [
                'SET ?set',
                [
                    [
                        'x' => '2',
                        'y' => 2,
                    ],
                ],
                'SET `x`="2", `y`=2',
            ],
        ];
    }
}
