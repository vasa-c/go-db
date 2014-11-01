<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Helpers;

use go\DB\Helpers\MapFields;

/**
 * @coversDefaultClass go\DB\Helpers\MapFields
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class MapFieldsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $map = array(
        'one' => 'o',
        'two' => 't',
    );

    /**
     * @covers ::col
     * @covers ::cols
     * @covers ::set
     * @covers ::where
     * @covers ::assoc
     * @covers ::row
     * @dataProvider providerMap
     * @param string $method
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
        return array(
            array(
                'col',
                'one',
                'o',
            ),
            array(
                'col',
                'three',
                'three',
            ),
            array(
                'col',
                array('t', 'one'),
                array('t', 'one'),
            ),
            array(
                'cols',
                null,
                null,
            ),
            array(
                'cols',
                array('one', array('t', 'two'), 'three'),
                array('o', array('t', 'two'), 'three'),
            ),
            array(
                'cols',
                'one',
                'o',
            ),
            array(
                'set',
                array('one' => 1, 'two' => 2, 'three' => 3),
                array('o' => 1, 't' => 2, 'three' => 3),
            ),
            array(
                'set',
                array('one' => 1, 'two' => 2, 'three' => 3),
                array('o' => 1, 't' => 2, 'three' => 3),
            ),
            array(
                'set',
                array('one' => array('col' => 'one', 'value' => 1), 'two' => array('value' => 2)),
                array('o' => array('col' => 'o', 'value' => 1), 't' => array('value' => 2)),
            ),
            array(
                'where',
                true,
                true,
            ),
            array(
                'where',
                array('one' => 1, 'two' => array(1, 2, 3)),
                array('o' => 1, 't' => array(1, 2, 3)),
            ),
            array(
                'where',
                array('one' => array('op' => '=', 'col' => 'two', 'value' => 3), 'three' => array(1, 2, 3)),
                array('o' => array('op' => '=', 'col' => 't', 'value' => 3), 'three' => array(1, 2, 3)),
            ),
            array(
                'order',
                null,
                null,
            ),
            array(
                'order',
                'one',
                'o',
            ),
            array(
                'order',
                'three',
                'three',
            ),
            array(
                'order',
                array('one', 'four', 'two'),
                array('o', 'four', 't'),
            ),
            array(
                'order',
                array('one', 'four', 'two' => true),
                array('o', 'four', 't' => true),
            ),
            array(
                'assoc',
                array(
                    array('id' => 1, 'o' => 2, 't' => 3),
                    array('id' => 4, 'o' => 5, 't' => 6),
                ),
                array(
                    array('id' => 1, 'one' => 2, 'two' => 3),
                    array('id' => 4, 'one' => 5, 'two' => 6),
                ),
            ),
            array(
                'row',
                array('id' => 1, 'o' => 2, 't' => 3),
                array('id' => 1, 'one' => 2, 'two' => 3),
            ),
            array(
                'row',
                null,
                null,
            ),
        );
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
