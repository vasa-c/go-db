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
final class QMarkTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        return array(
            array(
                'INSERT INTO `table` SET `a`="who??", `b`=?',
                array('who?'),
                'INSERT INTO `table` SET `a`="who?", `b`="who?"',
            ),
            array(
                'INSERT INTO `table` SET `a`="who??;", `b`=?',
                array('who?'),
                'INSERT INTO `table` SET `a`="who?", `b`="who?"',
            ),
        );
    }
}
