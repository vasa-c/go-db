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
final class NamedTest extends Base
{
    /**
     * {@inheritdoc}
     */
    public function providerTemplater()
    {
        $user = array(
            'userId'  => 7,
            'name'    => 'Vasa',
            'surname' => 'Pe"ta',
            'age'     => '35',
            'active'  => true,
        );
        return array(
            array(
                'INSERT INTO `users` SET `name`=?:name,`surname`=?:surname,`age`=?i:age,`active`=?b:active',
                $user,
                'INSERT INTO `users` SET `name`="Vasa",`surname`="Pe\"ta",`age`=35,`active`=1'
            ),
            array(
                'SELECT * FROM `users` WHERE (`name`=?:name AND `age`=35) OR (`name`=?:name AND `age`=7)',
                $user,
                'SELECT * FROM `users` WHERE (`name`="Vasa" AND `age`=35) OR (`name`="Vasa" AND `age`=7)'
            ),
        );
    }

    /**
     * ("SELECT 1", [0, 1]) - Error
     * ("SELECT 1", ['id' => 1]) - Ok (named placeholders)
     *
     * @param array $data
     * @param boolean $exception [optional]
     * @dataProvider providerNamedAndEmptyPattern
     */
    public function testNamedAndEmptyPattern($data, $exception = false)
    {
        $pattern = 'SELECT 1';
        $templater = $this->createTemplater($pattern, $data);
        if ($exception) {
            $this->setExpectedException('\go\DB\Exceptions\DataMuch');
            $templater->parse();
        } else {
            $templater->parse();
            $this->assertSame($pattern, $templater->getQuery());
        }
    }

    /**
     * @return array
     */
    public function providerNamedAndEmptyPattern()
    {
        return array(
            array(null, false),
            array(array(), false),
            array(array('id' => 1, 'x' => 2), false),
            array(array(1, 2), true),
        );
    }
}
