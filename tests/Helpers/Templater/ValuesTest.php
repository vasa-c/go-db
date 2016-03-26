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
        $values = array(
            array(0, 1, 2),
            array('one', null, 'three'),
        );
        return array(
            array(
                'INSERT INTO `table` VALUES ?values;',
                array($values),
                'INSERT INTO `table` VALUES ("0", "1", "2"), ("one", NULL, "three")',
            ),
            array(
                'INSERT INTO `table` VALUES ?vn',
                array($values),
                'INSERT INTO `table` VALUES ("0", "1", "2"), ("one", NULL, "three")',
            ),
            array(
                'INSERT INTO `table` VALUES ?values-bool-null',
                array($values),
                'INSERT INTO `table` VALUES (0, 1, 1), (1, NULL, 1)',
            ),
        );
    }
}
