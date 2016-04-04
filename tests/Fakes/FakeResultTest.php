<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Fakes;

use go\DB\Fakes\FakeResult;

/**
 * coversDefaultClass go\DB\Fakes\Helpers\FakeResult
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class FakesResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $method
     * @param string $param
     * @param mixed $expected
     * @dataProvider providerResult
     */
    public function testResult($method, $param, $expected)
    {
        $rows = [
            ['a' => 1, 'b' => 2],
            ['a' => 3, 'b' => 4],
        ];
        $result = new FakeResult($rows, 3, 5);
        $actual = $result->$method($param);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function providerResult()
    {
        return [
            [
                'assoc',
                null,
                [
                    ['a' => 1, 'b' => 2],
                    ['a' => 3, 'b' => 4],
                ],
            ],
            [
                'assoc',
                'b',
                [
                    2 => ['a' => 1, 'b' => 2],
                    4 => ['a' => 3, 'b' => 4],
                ],
            ],
            [
                'numerics',
                null,
                [
                    [1, 2],
                    [3, 4],
                ],
            ],
            [
                'numerics',
                1,
                [
                    2 => [1, 2],
                    4 => [3, 4],
                ],
            ],
            [
                'objects',
                null,
                [
                    (object)['a' => 1, 'b' => 2],
                    (object)['a' => 3, 'b' => 4],
                ],
            ],
            [
                'objects',
                'a',
                [
                    1 => (object)['a' => 1, 'b' => 2],
                    3 => (object)['a' => 3, 'b' => 4],
                ],
            ],
            [
                'col',
                null,
                [1, 3],
            ],
            [
                'vars',
                null,
                [
                    1 => 2,
                    3 => 4,
                ],
            ],
            [
                'row',
                null,
                ['a' => 1, 'b' => 2],
            ],
            [
                'object',
                null,
                (object)['a' => 1, 'b' => 2],
            ],
            [
                'el',
                null,
                1
            ],
            [
                'bool',
                null,
                true,
            ],
            [
                'num',
                null,
                2,
            ],
            [
                'id',
                null,
                3,
            ],
            [
                'ar',
                null,
                5,
            ],
            [
                'fetch',
                'assoc:b',
                [
                    2 => ['a' => 1, 'b' => 2],
                    4 => ['a' => 3, 'b' => 4],
                ]
            ],
        ];
    }

    public function testIterators()
    {
        $rows = [
            ['a' => 1, 'b' => 2],
            ['a' => 3, 'b' => 4],
        ];
        $result = new FakeResult($rows, 3, 5);
        $this->assertCount(2, $result);
        $this->assertEquals($rows, iterator_to_array($result));
        $this->assertEquals($rows, iterator_to_array($result->iassoc()));
        $this->assertEquals([[1, 2], [3, 4]], iterator_to_array($result->inumerics()));
        $this->assertEquals([2 => [1, 2], 4 => [3, 4]], iterator_to_array($result->inumerics(1)));
    }
}
