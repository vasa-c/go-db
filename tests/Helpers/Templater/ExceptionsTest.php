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

    /**
     * @dataProvider providerExceptionDataInvalidFormat
     * @param string $placeholder
     * @param mixed $data
     * @param string $message
     */
    public function testExceptionDataInvalidFormat($placeholder, $data, $message)
    {
        $pattern = '?'.$placeholder;
        $data = [$data];
        $templater = $this->createTemplater($pattern, $data);
        $this->setExpectedException('go\DB\Exceptions\DataInvalidFormat', $message);
        $templater->parse();
    }

    public function providerExceptionDataInvalidFormat()
    {
        return [
            'col' => [
                'col',
                [
                    'as' => 'z',
                ],
                'required `col`, `value` or `func` field',
            ],
            'string' => [
                'scalar',
                [1, 2],
                'required scalar',
            ],
            'int' => [
                'i',
                [1, 2],
                'required scalar',
            ],
            'list' => [
                'l',
                5,
                'required array (list of values)',
            ],
            'list-in' => [
                'l',
                [1, [2, 3], 4],
                'required scalar in item #1',
            ],
            'set' => [
                's',
                5,
                'required array (column => value)',
            ],
            'set-col' => [
                's',
                [
                    'x' => [
                        'as' => 5,
                    ],
                ],
                'required `col`, `value` or `func` field',
            ],
            'values' => [
                'v',
                'string',
                'required array of arrays'
            ],
            'values-in' => [
                'v',
                [[1, 2], [3, 4], 5, [6, 7]],
                'required array (list of values)',
            ],
            'table' => [
                't',
                [],
                'required `table` field',
            ],
            'escape' => [
                'e',
                [],
                'required string',
            ],
            'query' => [
                'q',
                [],
                'required string',
            ],
        ];
    }

    /**
     * @dataProvider providerExceptionSubDataInvalidFormat
     * @param string $placeholder
     * @param mixed $data
     * @param string $exceptionClass
     * @param string $message
     */
    public function testExceptionSubDataInvalidFormat($placeholder, $data, $exceptionClass, $message)
    {
        $pattern = '?'.$placeholder;
        $data = [$data];
        $templater = $this->createTemplater($pattern, $data);
        $this->setExpectedException($exceptionClass, $message);
        $templater->parse();
    }

    public function providerExceptionSubDataInvalidFormat()
    {
        return [
            'type' => [
                'v[?i, ?set-int]',
                [1, 2],
                'go\DB\Exceptions\SubDataInvalidFormat',
                'Only modifiers can be used. Found type: s',
            ],
            'internal' => [
                'l[?i, ?wtf]',
                [1, 2],
                'go\DB\Exceptions\SubDataInvalidFormat',
                'Invalid sub data for ?l: Unknown placeholder "wtf"',
            ],
            'mixedSubPlaceholder' => [
                'l[?i, ?i:foo]',
                [1, 2],
                'go\DB\Exceptions\SubDataInvalidFormat',
                'Invalid sub data for ?l: Mixed placeholder "?i:foo"',
            ],
            'mixedSubPlaceholder2' => [
                'v[?i:bar, ?i]',
                [1, 2],
                'go\DB\Exceptions\SubDataInvalidFormat',
                'Invalid sub data for ?v: Mixed placeholder "?i"',
            ],
            'listElementModifierNotSet' => [
                'l[?i, ?i]',
                [1, 2, 3],
                'go\DB\Exceptions\DataInvalidFormat',
                'Data for ?list has invalid format: "No modifier for key: 2"',
            ],
            'setElementModifierNotSet' => [
                's[?i:foo, ?i:baZ]',
                ['foo' => 1, 'bar' => 2],
                'go\DB\Exceptions\DataInvalidFormat',
                'Data for ?set has invalid format: "No modifier for col: bar"',
            ],
        ];
    }
}
