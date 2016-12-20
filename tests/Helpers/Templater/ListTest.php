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
final class ListTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        $list = ['str"ing', 1, null, '3.5'];
        return [
            'short' => [
                'INSERT INTO `table` VALUES (?l)',
                [$list],
                'INSERT INTO `table` VALUES ("str\"ing", 1, NULL, "3.5")',
            ],
            'full' => [
                'INSERT INTO `table` VALUES (?list)',
                [$list],
                'INSERT INTO `table` VALUES ("str\"ing", 1, NULL, "3.5")',
            ],
            'null' => [
                'INSERT INTO `table` VALUES (?ln)',
                [$list],
                'INSERT INTO `table` VALUES ("str\"ing", 1, NULL, "3.5")',
            ],
            'int' => [
                'INSERT INTO `table` VALUES (?li)',
                [$list],
                'INSERT INTO `table` VALUES (0, 1, NULL, 3)',
            ],
            'mixed' => [
                'INSERT INTO `table` VALUES (?lin)',
                [$list],
                'INSERT INTO `table` VALUES (0, 1, NULL, 3)',
            ],
        ];
    }
}
