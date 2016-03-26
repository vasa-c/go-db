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
final class QueryTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        return array(
            array(
                'SELECT * FROM ?t ?q ORDER BY ?c DESC',
                array('table', 'WHERE `q`="?"', 'c'),
                'SELECT * FROM `table` WHERE `q`="?" ORDER BY `c` DESC',
            ),
        );
    }
}
