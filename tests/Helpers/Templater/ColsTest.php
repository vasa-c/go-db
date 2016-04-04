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
final class ColsTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        return [
            'cols' => [
                'SELECT ?cols',
                [
                    [
                        'id',
                        ['a', 'b'],
                        [
                            'col' => 'value',
                            'func' => 'SUM',
                            'as' => 's',
                        ],
                    ],
                ],
                'SELECT `id`,`p_a`.`b`,SUM(`value`) AS `s`',
                'p_'
            ],
        ];
    }
}
