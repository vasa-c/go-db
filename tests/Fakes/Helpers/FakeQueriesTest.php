<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Fakes\Helpers;

use go\DB\Fakes\Helpers\FakeQueries;

/**
 * coversDefaultClass go\DB\Fakes\Helpers\FaleQueries
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class FakesQueriesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::insert
     * @param mixed $where
     * @param array $expected
     * @dataProvider providerWhere
     */
    public function testWhere($where, $expected)
    {
        $data = [
            2 => ['id' => 2, 'name' => 'One', 'age' => 4],
            5 => ['id' => 5, 'name' => 'Five', 'age' => 10],
            12 => ['id' => 12, 'name' => 'Twelve', 'age' => 24],
            7 => ['id' => 7, 'name' => 'Seven', 'age' => 14],
            20 => ['id' => 20, 'name' => null, 'age' => 40],
            6 => ['id' => 6, 'name' => 'Six', 'age' => 12],
        ];
        $actual = FakeQueries::where($data, $where);
        $this->assertSame($actual, $expected);
    }

    /**
     * @return array
     */
    public function providerWhere()
    {
        return [
            [
                true,
                [
                    2 => ['id' => 2, 'name' => 'One', 'age' => 4],
                    5 => ['id' => 5, 'name' => 'Five', 'age' => 10],
                    12 => ['id' => 12, 'name' => 'Twelve', 'age' => 24],
                    7 => ['id' => 7, 'name' => 'Seven', 'age' => 14],
                    20 => ['id' => 20, 'name' => null, 'age' => 40],
                    6 => ['id' => 6, 'name' => 'Six', 'age' => 12],
                ],
            ],
            [
                [],
                [
                    2 => ['id' => 2, 'name' => 'One', 'age' => 4],
                    5 => ['id' => 5, 'name' => 'Five', 'age' => 10],
                    12 => ['id' => 12, 'name' => 'Twelve', 'age' => 24],
                    7 => ['id' => 7, 'name' => 'Seven', 'age' => 14],
                    20 => ['id' => 20, 'name' => null, 'age' => 40],
                    6 => ['id' => 6, 'name' => 'Six', 'age' => 12],
                ],
            ],
            [
                false,
                [
                ],
            ],
            [
                ['id' => 5],
                [
                    5 => ['id' => 5, 'name' => 'Five', 'age' => 10],
                ],
            ],
            [
                ['id' => 9],
                [
                ],
            ],
            [
                ['id' => [5, 9, 2]],
                [
                    2 => ['id' => 2, 'name' => 'One', 'age' => 4],
                    5 => ['id' => 5, 'name' => 'Five', 'age' => 10],
                ],
            ],
            [
                ['id' => [5, 9, 2], 'age' => 4],
                [
                    2 => ['id' => 2, 'name' => 'One', 'age' => 4],
                ],
            ],
            [
                ['id' => [5, 9, 2], 'age' => 3],
                [
                ],
            ],
            [
                ['id' => [5, 9, 2], 'age' => [10, 18, 5]],
                [
                    5 => ['id' => 5, 'name' => 'Five', 'age' => 10],
                ],
            ],
            [
                ['id' => [20, 5, 7], 'name' => null],
                [
                    20 => ['id' => 20, 'name' => null, 'age' => 40],
                ],
            ],
            [
                ['id' => [20, 5, 7], 'name' => true],
                [
                    5 => ['id' => 5, 'name' => 'Five', 'age' => 10],
                    7 => ['id' => 7, 'name' => 'Seven', 'age' => 14],
                ],
            ],
        ];
    }

    /**
     * covers ::order
     * @param mixed $order
     * @param array $expected
     * @dataProvider providerOrder
     */
    public function testOrder($order, $expected)
    {
        $data = [
            ['id' => 2, 'name' => 'One', 'age' => 4],
            ['id' => 5, 'name' => 'Five', 'age' => 10],
            ['id' => 12, 'name' => 'Twelve', 'age' => 10],
            ['id' => 7, 'name' => 'Seven', 'age' => 10],
            ['id' => 20, 'name' => null, 'age' => 40],
            ['id' => 6, 'name' => 'Six', 'age' => 12],
        ];
        $actual = FakeQueries::order($data, $order);
        $this->assertSame($actual, $expected);
    }

    /**
     * covers ::cols
     */
    public function providerOrder()
    {
        return [
            [
                null,
                [
                    ['id' => 2, 'name' => 'One', 'age' => 4],
                    ['id' => 5, 'name' => 'Five', 'age' => 10],
                    ['id' => 12, 'name' => 'Twelve', 'age' => 10],
                    ['id' => 7, 'name' => 'Seven', 'age' => 10],
                    ['id' => 20, 'name' => null, 'age' => 40],
                    ['id' => 6, 'name' => 'Six', 'age' => 12],
                ],
            ],
            [
                'id',
                [
                    ['id' => 2, 'name' => 'One', 'age' => 4],
                    ['id' => 5, 'name' => 'Five', 'age' => 10],
                    ['id' => 6, 'name' => 'Six', 'age' => 12],
                    ['id' => 7, 'name' => 'Seven', 'age' => 10],
                    ['id' => 12, 'name' => 'Twelve', 'age' => 10],
                    ['id' => 20, 'name' => null, 'age' => 40],

                ],
            ],
            [
                ['id' => true],
                [
                    ['id' => 2, 'name' => 'One', 'age' => 4],
                    ['id' => 5, 'name' => 'Five', 'age' => 10],
                    ['id' => 6, 'name' => 'Six', 'age' => 12],
                    ['id' => 7, 'name' => 'Seven', 'age' => 10],
                    ['id' => 12, 'name' => 'Twelve', 'age' => 10],
                    ['id' => 20, 'name' => null, 'age' => 40],
                ],
            ],
            [
                ['id' => false],
                [
                    ['id' => 20, 'name' => null, 'age' => 40],
                    ['id' => 12, 'name' => 'Twelve', 'age' => 10],
                    ['id' => 7, 'name' => 'Seven', 'age' => 10],
                    ['id' => 6, 'name' => 'Six', 'age' => 12],
                    ['id' => 5, 'name' => 'Five', 'age' => 10],
                    ['id' => 2, 'name' => 'One', 'age' => 4],
                ],
            ],
            [
                ['age' => true, 'id' => false],
                [
                    ['id' => 2, 'name' => 'One', 'age' => 4],
                    ['id' => 12, 'name' => 'Twelve', 'age' => 10],
                    ['id' => 7, 'name' => 'Seven', 'age' => 10],
                    ['id' => 5, 'name' => 'Five', 'age' => 10],
                    ['id' => 6, 'name' => 'Six', 'age' => 12],
                    ['id' => 20, 'name' => null, 'age' => 40],
                ],
            ],
            [
                ['age' => false, 'name' => true],
                [
                    ['id' => 20, 'name' => null, 'age' => 40],
                    ['id' => 6, 'name' => 'Six', 'age' => 12],
                    ['id' => 5, 'name' => 'Five', 'age' => 10],
                    ['id' => 7, 'name' => 'Seven', 'age' => 10],
                    ['id' => 12, 'name' => 'Twelve', 'age' => 10],
                    ['id' => 2, 'name' => 'One', 'age' => 4],
                ],
            ],
        ];
    }

    /**
     * covers ::order
     * @param mixed $limit
     * @param array $expected
     * @dataProvider providerLimit
     */
    public function testLimit($limit, $expected)
    {
        $data = [
            ['id' => 2, 'name' => 'One', 'age' => 4],
            ['id' => 5, 'name' => 'Five', 'age' => 10],
            ['id' => 12, 'name' => 'Twelve', 'age' => 10],
            ['id' => 7, 'name' => 'Seven', 'age' => 10],
            ['id' => 20, 'name' => null, 'age' => 40],
            ['id' => 6, 'name' => 'Six', 'age' => 12],
        ];
        $actual = FakeQueries::limit($data, $limit);
        $this->assertSame($actual, $expected);
    }

    /**
     * @return array
     */
    public function providerLimit()
    {
        return [
            [
                null,
                [
                    ['id' => 2, 'name' => 'One', 'age' => 4],
                    ['id' => 5, 'name' => 'Five', 'age' => 10],
                    ['id' => 12, 'name' => 'Twelve', 'age' => 10],
                    ['id' => 7, 'name' => 'Seven', 'age' => 10],
                    ['id' => 20, 'name' => null, 'age' => 40],
                    ['id' => 6, 'name' => 'Six', 'age' => 12],
                ],
            ],
            [
                3,
                [
                    ['id' => 2, 'name' => 'One', 'age' => 4],
                    ['id' => 5, 'name' => 'Five', 'age' => 10],
                    ['id' => 12, 'name' => 'Twelve', 'age' => 10],
                ],
            ],
            [
                [2, 3],
                [
                    ['id' => 12, 'name' => 'Twelve', 'age' => 10],
                    ['id' => 7, 'name' => 'Seven', 'age' => 10],
                    ['id' => 20, 'name' => null, 'age' => 40],
                ],
            ],
            [
                [4, 10],
                [
                    ['id' => 20, 'name' => null, 'age' => 40],
                    ['id' => 6, 'name' => 'Six', 'age' => 12],
                ],
            ],
            [
                [40, 10],
                [
                ],
            ],
            [
                [2, 0],
                [
                ],
            ],
            [
                0,
                [
                ],
            ],
        ];
    }
}
