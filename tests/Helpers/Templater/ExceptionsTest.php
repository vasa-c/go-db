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
final class ExceptionsTest extends BaseTemplater
{

    /**
     * @param string $pattern
     * @param array $data
     * @dataProvider providerExceptionUnknownPlaceholder
     * @expectedException \go\DB\Exceptions\UnknownPlaceholder
     */
    public function testExceptionUnknownPlaceholder($pattern, $data)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
    }

    /**
     * @return array
     */
    public function providerExceptionUnknownPlaceholder()
    {
        return array(
            array(
                'SELECT * FROM ?wtf;',
                array(1)
            ),
            array(
                'SELECT * FROM ?lu',
                array(1)
            ),
            array(
                'SELECT * FROM ?list-u',
                array(1)
            ),
            array(
                'SELECT * FROM ?list:',
                array(1)
            ),
        );
    }

    /**
     * @param string $pattern
     * @param array $data
     * @dataProvider providerExceptionDataNotEnough
     * @expectedException \go\DB\Exceptions\DataNotEnough
     */
    public function testExceptionDataNotEnough($pattern, $data)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
    }

    /**
     * @return array
     */
    public function providerExceptionDataNotEnough()
    {
        return array(
            array(
                'INSERT INTO `table` VALUES (?,?,?)',
                array(1, 2)
            ),
        );
    }

    /**
     * @param string $pattern
     * @param array $data
     * @dataProvider providerExceptionDataMuch
     * @expectedException \go\DB\Exceptions\DataMuch
     */
    public function testExceptionDataMuch($pattern, $data)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
    }

    /**
     * @return array
     */
    public function providerExceptionDataMuch()
    {
        return array(
            array(
                'INSERT INTO `table` VALUES (?,?,?)',
                array(1, 2, 3, 4)
            ),
        );
    }



    /**
     * @param string $pattern
     * @param array $data
     * @dataProvider providerExceptionDataNamed
     * @expectedException \go\DB\Exceptions\DataNamed
     */
    public function testExceptionDataNamed($pattern, $data)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
    }

    /**
     * @return array
     */
    public function providerExceptionDataNamed()
    {
        return array(
            array(
                'INSERT INTO `table` VALUES (?:a,?i:b,?:c)',
                array('a' => 1, 'b' => 2)
            ),
        );
    }

    /**
     * @param string $pattern
     * @param array $data
     * @dataProvider providerExceptionDataMixed
     * @expectedException \go\DB\Exceptions\DataNamed
     */
    public function testExceptionDataMixed($pattern, $data)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
    }

    /**
     * @return array
     */
    public function providerExceptionDataMixed()
    {
        return array(
            array(
                'INSERT INTO `table` VALUES (?:a,?i:b,?:c)',
                array('a' => 1, 'b' => 2)
            ),
        );
    }
}
