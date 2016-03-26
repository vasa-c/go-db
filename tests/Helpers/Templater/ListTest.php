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
        $list = array('str"ing', 1, null, '3.5');
        return array(
            array(
                'INSERT INTO `table` VALUES (?l)',
                array($list),
                'INSERT INTO `table` VALUES ("str\"ing", "1", NULL, "3.5")',
            ),
            array(
                'INSERT INTO `table` VALUES (?list)',
                array($list),
                'INSERT INTO `table` VALUES ("str\"ing", "1", NULL, "3.5")',
            ),
            array(
                'INSERT INTO `table` VALUES (?ln)',
                array($list),
                'INSERT INTO `table` VALUES ("str\"ing", "1", NULL, "3.5")',
            ),
            array(
                'INSERT INTO `table` VALUES (?li)',
                array($list),
                'INSERT INTO `table` VALUES (0, 1, NULL, 3)',
            ),
            array(
                'INSERT INTO `table` VALUES (?lin)',
                array($list),
                'INSERT INTO `table` VALUES (0, 1, NULL, 3)',
            ),
        );
    }
}
