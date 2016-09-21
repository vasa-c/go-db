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
final class ScalarTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        $data = ['str"ing', 1, null, '3.5', 2.5];
        return [
            'escape' => [
                'INSERT INTO `table` VALUES (?, ?scalar, ?, ?string, ?)',
                $data,
                'INSERT INTO `table` VALUES ("str\"ing", 1, NULL, "3.5", 2.5)',
            ],
            'null' => [
                'INSERT INTO `table` VALUES (?null, ?null, ?null, ?null, ?null)',
                $data,
                'INSERT INTO `table` VALUES ("str\"ing", 1, NULL, "3.5", 2.5)',
            ],
            'int' => [
                'INSERT INTO `table` VALUES (?i, ?i, ?i, ?i, ?i)',
                $data,
                'INSERT INTO `table` VALUES (0, 1, NULL, 3, 2)',
            ],
            'int-null' => [
                'INSERT INTO `table` VALUES (?in, ?in, ?in, ?in, ?in)',
                $data,
                'INSERT INTO `table` VALUES (0, 1, NULL, 3, 2)',
            ],
            'float' => [
                'INSERT INTO `table` VALUES (?f, ?f, ?f, ?f, ?f)',
                $data,
                'INSERT INTO `table` VALUES (0, 1, NULL, 3.5, 2.5)',
            ],
            'string' => [
                'INSERT INTO `table` VALUES (?string, ?string, ?string, ?string, ?string)',
                $data,
                'INSERT INTO `table` VALUES ("str\"ing", "1", NULL, "3.5", "2.5")',
            ],
            'string-null' => [
                'INSERT INTO `table` VALUES (?string-null, ?string-null, ?string-null, ?string-null, ?string-null)',
                $data,
                'INSERT INTO `table` VALUES ("str\"ing", "1", NULL, "3.5", "2.5")',
            ],
            'full' => [
                'INSERT INTO `table` VALUES (?string, ?scalar-int, ?scalar-null, ?scalar-int-null, ?scalar-float)',
                $data,
                'INSERT INTO `table` VALUES ("str\"ing", 1, NULL, 3, 2.5)',
            ],
        ];
    }
}
