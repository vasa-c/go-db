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
final class ValuesTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        $values = [
            [0, 1, 2],
            ['one', null, 'three'],
        ];
        return [
            'values' => [
                'INSERT INTO `table` VALUES ?values;',
                [$values],
                'INSERT INTO `table` VALUES (0, 1, 2), ("one", NULL, "three")',
            ],
            'subset' => [
                'INSERT INTO `table` VALUES ?values[?string, ?int-null, ?i]',
                [$values],
                'INSERT INTO `table` VALUES ("0", 1, 2), ("one", NULL, 0)',
            ],
            'null' => [
                'INSERT INTO `table` VALUES ?vn',
                [$values],
                'INSERT INTO `table` VALUES (0, 1, 2), ("one", NULL, "three")',
            ],
            'int' => [
                'INSERT INTO `table` VALUES ?values-int',
                [$values],
                'INSERT INTO `table` VALUES (0, 1, 2), (0, NULL, 0)',
            ],
            'bool' => [
                'INSERT INTO `table` VALUES ?values-bool-null',
                [$values],
                'INSERT INTO `table` VALUES (0, 1, 1), (1, NULL, 1)',
            ],
        ];
    }
}
