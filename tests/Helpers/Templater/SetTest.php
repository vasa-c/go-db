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
                'UPDATE `test` SET `x`=`x`+1, `y`="2" WHERE `id`=10',
            ],
            'extended' => [
                'SET ?s',
                [
                    [
                        'a' => [],
                        'b' => ['col' => 'x'],
                        'c' => ['col' => 'x', 'value' => -2],
                        'd' => ['value' => -3]
                    ],
                ],
                'SET `a`=NULL, `b`=`x`, `c`=`x`-2, `d`="-3"',
            ],
        ];
    }
}
