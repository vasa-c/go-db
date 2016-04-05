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
final class TableTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        return [
            'string' => [
                'TRUNCATE TABLE ?table',
                [
                    'test',
                ],
                'TRUNCATE TABLE `p_test`',
                'p_',
            ],
            'list' => [
                'TRUNCATE TABLE ?table',
                [
                    ['one', 'two', 'three', 'four'],
                ],
                'TRUNCATE TABLE `one`.`two`.`three`.`p_four`',
                'p_',
            ],
            'assoc' => [
                'TRUNCATE TABLE ?table',
                [
                    [
                        'db' => 'dbname',
                        'table' => 'test',
                    ],
                ],
                'TRUNCATE TABLE `dbname`.`p_test`',
                'p_',
            ],
            'without_db' => [
                'TRUNCATE TABLE ?table',
                [
                    [
                        'table' => 'test',
                    ],
                ],
                'TRUNCATE TABLE `p_test`',
                'p_',
            ],
            'chain' => [
                'SELECT * FROM ?t',
                [
                    [
                        'table' => ['public','test'],
                    ],
                ],
                'SELECT * FROM `public`.`p_test`',
                'p_'
            ],
            'as' => [
                'SELECT * FROM ?t',
                [
                    [
                        'table' => 'test',
                        'as' => 't1'
                    ],
                ],
                'SELECT * FROM `p_test` AS `t1`',
                'p_'
            ]
        ];
    }
}
