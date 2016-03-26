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
final class EscapeTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        return [
            'escape' => [
                'SELECT * FROM `table` WHERE `name` LIKE "%?e%" AND `e`=?i',
                ['qwe"rty', '2qwe"rty'],
                'SELECT * FROM `table` WHERE `name` LIKE "%qwe\"rty%" AND `e`=2',
            ],
        ];
    }
}
