<?php
/**
 * @package go\DB
 */

namespace go\Tests\DB\Helpers;

use go\DB\Helpers\MapFields;

/**
 * @coversDefaultClass go\DB\Helpers\MapFields
 */
class MapFieldsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var go\DB\Helpers\MapFields
     */
    private $map = [
        'one' => 'o',
        'two' => 't',
    ];

    /**
     * @covers ::col
     * @covers ::cols
     * @covers ::set
     * @covers ::where
     * @covers ::assoc
     * @covers ::row
     * @dataProvider providerMap
     * @param mixed $value
     * @param mixed $expected
     */
    public function testMap($method, $value, $expected)
    {
        $map = new MapFields($this->map);
        $this->assertEquals($expected, $map->$method($value));
    }

    /**
     * @return array
     */
    public function providerMap()
    {
        return [
            [
                'col',
                'one',
                'o',
            ],
            [
                'col',
                'three',
                'three',
            ],
            [
                'col',
                ['t', 'one'],
                ['t', 'one'],
            ],
            [
                'cols',
                null,
                null,
            ],
            [
                'cols',
                ['one', ['t', 'two'], 'three'],
                ['o', ['t', 'two'], 'three'],
            ],
            [
                'cols',
                'one',
                'o',
            ],
            [
                'set',
                ['one' => 1, 'two' => 2, 'three' => 3],
                ['o' => 1, 't' => 2, 'three' => 3],
            ],
            [
                'set',
                ['one' => 1, 'two' => 2, 'three' => 3],
                ['o' => 1, 't' => 2, 'three' => 3],
            ],
            [
                'set',
                ['one' => ['col' => 'one', 'value' => 1], 'two' => ['value' => 2]],
                ['o' => ['col' => 'o', 'value' => 1], 't' => ['value' => 2]],
            ],
            [
                'where',
                true,
                true,
            ],
            [
                'where',
                ['one' => 1, 'two' => [1, 2, 3]],
                ['o' => 1, 't' => [1, 2, 3]],
            ],
            [
                'where',
                ['one' => ['op' => '=', 'col' => 'two', 'value' => 3], 'three' => [1, 2, 3]],
                ['o' => ['op' => '=', 'col' => 't', 'value' => 3], 'three' => [1, 2, 3]],
            ],
            [
                'order',
                null,
                null,
            ],
            [
                'order',
                'one',
                'o',
            ],
            [
                'order',
                'three',
                'three',
            ],
            [
                'order',
                ['one', 'four', 'two'],
                ['o', 'four', 't'],
            ],
            [
                'order',
                ['one', 'four', 'two' => true],
                ['o', 'four', 't' => true],
            ],
            [
                'assoc',
                [
                    ['id' => 1, 'o' => 2, 't' => 3],
                    ['id' => 4, 'o' => 5, 't' => 6],
                ],
                [
                    ['id' => 1, 'one' => 2, 'two' => 3],
                    ['id' => 4, 'one' => 5, 'two' => 6],
                ],
            ],
            [
                'row',
                ['id' => 1, 'o' => 2, 't' => 3],
                ['id' => 1, 'one' => 2, 'two' => 3],
            ],
            [
                'row',
                null,
                null,
            ],
        ];
    }

    /**
     * @covers ::getMap
     */
    public function testGetMap()
    {
        $map = new MapFields($this->map);
        $this->assertEquals($this->map, $map->getMap());
    }
}
