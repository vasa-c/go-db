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
        return [
            'ph' => [
                'SELECT * FROM ?wtf;',
                [1]
            ],
            'modifier' => [
                'SELECT * FROM ?lu',
                [1]
            ],
            'long_modifier' => [
                'SELECT * FROM ?list-u',
                [1]
            ],
            'no-name' => [
                'SELECT * FROM ?list:',
                [1]
            ],
        ];
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
        return [
            [
                'INSERT INTO `table` VALUES (?,?,?)',
                [1, 2]
            ],
        ];
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
        return [
            [
                'INSERT INTO `table` VALUES (?,?,?)',
                [1, 2, 3, 4]
            ],
        ];
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
        return [
            [
                'INSERT INTO `table` VALUES (?:a,?i:b,?:c)',
                ['a' => 1, 'b' => 2]
            ],
        ];
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
        return [
            [
                'INSERT INTO `table` VALUES (?:a,?i:b,?:c)',
                ['a' => 1, 'b' => 2]
            ],
        ];
    }
}
