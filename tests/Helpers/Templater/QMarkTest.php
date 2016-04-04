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
        return [
            'q_mark' => [
                'INSERT INTO `table` SET `a`="who??", `b`=?',
                ['who?'],
                'INSERT INTO `table` SET `a`="who?", `b`="who?"',
            ],
            'q_mark_semicolon' => [
                'INSERT INTO `table` SET `a`="who??;", `b`=?',
                ['who?'],
                'INSERT INTO `table` SET `a`="who?", `b`="who?"',
            ],
        ];
    }
}
