<?php
/**
 * @package go\DB
 * @subpackage Tests
 */

namespace go\Tests\DB\Helpers;

use go\DB\Helpers\Connector;
use go\DB\Helpers\Templater;

/**
 * @coversDefaultClass go\DB\Helpers\Templater
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class TemplaterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * ?, ?string, ?scalar
     *
     * @param string $pattern
     * @param array data
     * @param string $expected
     * @dataProvider providerScalar
     */
    public function testScalar($pattern, $data, $expected)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }

    /**
     * @return array
     */
    public function providerScalar()
    {
        $data = array('str"ing', 1, null, '3.5');
        return array(
            array(
                'INSERT INTO `table` VALUES (?, ?scalar, ?, ?string)',
                $data,
                'INSERT INTO `table` VALUES ("str\"ing", "1", NULL, "3.5")',
            ),
            array(
                'INSERT INTO `table` VALUES (?null, ?null, ?null, ?null)',
                $data,
                'INSERT INTO `table` VALUES ("str\"ing", "1", NULL, "3.5")',
            ),
            array(
                'INSERT INTO `table` VALUES (?i, ?i, ?i, ?i)',
                $data,
                'INSERT INTO `table` VALUES (0, 1, NULL, 3)',
            ),
            array(
                'INSERT INTO `table` VALUES (?in, ?in, ?in, ?in)',
                $data,
                'INSERT INTO `table` VALUES (0, 1, NULL, 3)',
            ),
            array(
                'INSERT INTO `table` VALUES (?string, ?scalar-int, ?scalar-null, ?scalar-int-null)',
                $data,
                'INSERT INTO `table` VALUES ("str\"ing", 1, NULL, 3)',
            ),
        );
    }

    /**
     * ?l, ?list
     * @param string $pattern
     * @param array $data
     * @param string $expected
     * @dataProvider providerList
     */
    public function testList($pattern, $data, $expected)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }

    /**
     * @return array
     */
    public function providerList()
    {
        $list = array('str"ing', 1, null, '3.5');
        return array(
            array(
                'INSERT INTO `table` VALUES (?l)',
                array($list),
                'INSERT INTO `table` VALUES ("str\"ing", "1", NULL, "3.5")',
            ),
            array(
                'INSERT INTO `table` VALUES (?list)',
                array($list),
                'INSERT INTO `table` VALUES ("str\"ing", "1", NULL, "3.5")',
            ),
            array(
                'INSERT INTO `table` VALUES (?ln)',
                array($list),
                'INSERT INTO `table` VALUES ("str\"ing", "1", NULL, "3.5")',
            ),
            array(
                'INSERT INTO `table` VALUES (?li)',
                array($list),
                'INSERT INTO `table` VALUES (0, 1, NULL, 3)',
            ),
            array(
                'INSERT INTO `table` VALUES (?lin)',
                array($list),
                'INSERT INTO `table` VALUES (0, 1, NULL, 3)',
            ),
        );
    }

    /**
     * ?s, ?set
     * @param string $pattern
     * @param array $data
     * @param string $expected
     * @dataProvider providerSet
     */
    public function testSet($pattern, $data, $expected)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }

    /**
     * @return array
     */
    public function providerSet()
    {
        $set = array(
            's' => 'str"ing',
            'd' => '3.5',
            'n' => null,
        );
        return array(
            array(
                'INSERT INTO `table` SET ?s',
                array($set),
                'INSERT INTO `table` SET `s`="str\"ing", `d`="3.5", `n`=NULL',
            ),
            array(
                'INSERT INTO `table` SET ?set-null',
                array($set),
                'INSERT INTO `table` SET `s`="str\"ing", `d`="3.5", `n`=NULL',
            ),
            array(
                'INSERT INTO `table` SET ?sin',
                array($set),
                'INSERT INTO `table` SET `s`=0, `d`=3, `n`=NULL',
            ),
            array(
                'UPDATE ?t SET ?s WHERE `id`=?i',
                array('test', array('x' => array('col' => 'x', 'value' => 1), 'y' => 2), 10),
                'UPDATE `test` SET `x`=`x`+1, `y`="2" WHERE `id`=10',
            ),
            array(
                'SET ?sn',
                array(
                    array(
                        'a' => array(),
                        'b' => array('col' => 'x'),
                        'c' => array('col' => 'x', 'value' => -2),
                        'd' => array('value' => -3)),
                ),
                'SET `a`=NULL, `b`=`x`, `c`=`x`-2, `d`="-3"',
            ),
        );
    }

    /**
     * ?v, ?values
     * @param string $pattern
     * @param array $data
     * @param string $expected
     * @dataProvider providerValues
     */
    public function testValues($pattern, $data, $expected)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }

    /**
     * @return array
     */
    public function providerValues()
    {
        $values = array(
            array(0, 1, 2),
            array('one', null, 'three'),
        );
        return array(
            array(
                'INSERT INTO `table` VALUES ?values;',
                array($values),
                'INSERT INTO `table` VALUES ("0", "1", "2"), ("one", NULL, "three")',
            ),
            array(
                'INSERT INTO `table` VALUES ?vn',
                array($values),
                'INSERT INTO `table` VALUES ("0", "1", "2"), ("one", NULL, "three")',
            ),
            array(
                'INSERT INTO `table` VALUES ?values-bool-null',
                array($values),
                'INSERT INTO `table` VALUES (0, 1, 1), (1, NULL, 1)',
            ),
        );
    }

    /**
     * ?c, ?t, ?col, ?table
     * @param string $pattern
     * @param array $data
     * @param string $expected
     * @dataProvider providerField
     */
    public function testFields($pattern, $data, $expected)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }

    /**
     * @return array
     */
    public function providerField()
    {
        return array(
            array(
                'INSERT INTO ?t SET ?c=?i',
                array('table', 'col', 1),
                'INSERT INTO `table` SET `col`=1'
            ),
            array(
                'INSERT INTO ?table SET ?col=?i',
                array('table', array('t', 'col'), 1),
                'INSERT INTO `table` SET `t`.`col`=1'
            ),
            array(
                'SELECT ?cols FROM ?t',
                array(array('a', 'b', 'c', array('t', 'd')), 'table'),
                'SELECT `a`,`b`,`c`,`t`.`d` FROM `table`',
            ),
            array(
                'SELECT ?cols FROM ?t',
                array('single', 'table'),
                'SELECT `single` FROM `table`',
            ),
            array(
                'SELECT ?cols FROM ?t',
                array(array(), 'table'),
                'SELECT * FROM `table`',
            ),
            array(
                'SELECT ?cols FROM ?t',
                array(true, 'table'),
                'SELECT * FROM `table`',
            ),
        );
    }

    /**
     * ?w, ?where
     * @param string $pattern
     * @param array $data
     * @param string $expected
     * @param string $prefix [optional]
     * @dataProvider providerWhere
     */
    public function testWhere($pattern, $data, $expected, $prefix = null)
    {
        $templater = $this->createTemplater($pattern, $data, $prefix);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }

    /**
     * @return array
     */
    public function providerWhere()
    {
        return array(
            array(
                'WHERE ?w',
                array(array('x' => 1)),
                'WHERE `x`=1',
            ),
            array(
                'WHERE ?w',
                array(array('x' => 1, 'y' => 'qu"ot')),
                'WHERE `x`=1 AND `y`="qu\"ot"',
            ),
            array(
                'WHERE ?where',
                array(array('x' => null, 'y' => array(1, '2', 3))),
                'WHERE `x` IS NULL AND `y` IN (1,"2",3)',
            ),
            array(
                'WHERE ?where',
                array(array('one' => true, 'y' => '5', 'z' => 6)),
                'WHERE `one` IS NOT NULL AND `y`="5" AND `z`=6',
            ),
            array(
                'WHERE ?w',
                array(true),
                'WHERE 1=1',
            ),
            array(
                'WHERE ?w',
                array(array()),
                'WHERE 1=1',
            ),
            array(
                'WHERE ?w',
                array(false),
                'WHERE 1=0',
            ),
            array(
                'WHERE ?w',
                array(array('x' => 1, 'y' => array('op' => '<>', 'value' => 'xx'))),
                'WHERE `x`=1 AND `y`<>"xx"',
            ),
            array(
                'WHERE ?w',
                array(array('x' => 2, 'y' => array('op' => '<=', 'col' => 'x'))),
                'WHERE `x`=2 AND `y`<=`x`',
            ),
            array(
                'WHERE ?w',
                array(array('x' => 2, 'y' => array('op' => '=', 'col' => 'x', 'value' => 3))),
                'WHERE `x`=2 AND `y`=`x`+3',
            ),
            array(
                'WHERE ?w',
                array(array('x' => 2, 'y' => array('op' => '<>', 'col' => 'z', 'value' => -5))),
                'WHERE `x`=2 AND `y`<>`z`-5',
            ),
        );
    }

    /**
     * ?o, ?order
     * @param string $pattern
     * @param array $data
     * @param string $expected
     * @param string $prefix [optional]
     * @dataProvider providerOrder
     */
    public function testOrder($pattern, $data, $expected, $prefix = null)
    {
        $templater = $this->createTemplater($pattern, $data, $prefix);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }

    /**
     * @return array
     */
    public function providerOrder()
    {
        return array(
            array(
                'ORDER BY ?o',
                array('id'),
                'ORDER BY `id`',
            ),
            array(
                'ORDER BY ?order;',
                array(array('a', 'b', 'c')),
                'ORDER BY `a` ASC,`b` ASC,`c` ASC',
            ),
            array(
                'ORDER BY ?order',
                array(array('a' => false, 'b' => true, 'c' => false)),
                'ORDER BY `a` DESC,`b` ASC,`c` DESC',
            ),
            array(
                'ORDER BY ?order',
                array(array(array('t', 'a'), 'b' => false)),
                'ORDER BY `t`.`a` ASC,`b` DESC',
            ),
        );
    }

    /**
     * ?e, ?escape
     * @param string $pattern
     * @param array $data
     * @param string $expected
     * @dataProvider providerEscape
     */
    public function testEscape($pattern, $data, $expected)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }

    /**
     * @return array
     */
    public function providerEscape()
    {
        return array(
            array(
                'SELECT * FROM `table` WHERE `name` LIKE "%?e%" AND `e`=?i',
                array('qwe"rty', '2qwe"rty'),
                'SELECT * FROM `table` WHERE `name` LIKE "%qwe\"rty%" AND `e`=2'
            ),
        );
    }

    /**
     * ?q, ?query
     * @param string $pattern
     * @param array $data
     * @param string $expected
     * @dataProvider providerQuery
     */
    public function testQuery($pattern, $data, $expected)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }

    /**
     * @return array
     */
    public function providerQuery()
    {
        return array(
            array(
                'SELECT * FROM ?t ?q ORDER BY ?c DESC',
                array('table', 'WHERE `q`="?"', 'c'),
                'SELECT * FROM `table` WHERE `q`="?" ORDER BY `c` DESC',
            ),
        );
    }

    /**
     * ?? - insert a question mark
     * @param string $pattern
     * @param array $data
     * @param string $expected
     * @dataProvider providerQMark
     */
    public function testQMark($pattern, $data, $expected)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }

    /**
     * @return array
     */
    public function providerQMark()
    {
        return array(
            array(
                'INSERT INTO `table` SET `a`="who??", `b`=?',
                array('who?'),
                'INSERT INTO `table` SET `a`="who?", `b`="who?"',
            ),
            array(
                'INSERT INTO `table` SET `a`="who??;", `b`=?',
                array('who?'),
                'INSERT INTO `table` SET `a`="who?", `b`="who?"',
            ),
        );
    }

    /**
     * @param string $pattern
     * @param array $data
     * @param string $prefix
     * @param string $expected
     * @dataProvider providerPrefix
     */
    public function testPrefix($pattern, $data, $prefix, $expected)
    {
        $templater = $this->createTemplater($pattern, $data, $prefix);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }

    /**
     * @return array
     */
    public function providerPrefix()
    {
        $pattern = 'SELECT * FROM {table} AS `t` LEFT JOIN ?t ON ?t.?c=?c WHERE `id`=?';
        return array(
            array(
                $pattern,
                array('qwerty', 'qwerty', 'col', array('t', 'z'), 'value'),
                '',
                'SELECT * FROM `table` AS `t` LEFT JOIN `qwerty` ON `qwerty`.`col`=`t`.`z` WHERE `id`="value"'
            ),
            array(
                $pattern,
                array('qwerty', 'qwerty', 'col', array('t', 'z'), 'value'),
                'prefix_',
                'SELECT * FROM `prefix_table` AS `t` LEFT JOIN '.
                    '`prefix_qwerty` ON `prefix_qwerty`.`col`=`prefix_t`.`z` WHERE `id`="value"'
            ),
        );
    }

    /**
     * Named placeholders
     *
     * @param string $pattern
     * @param array $data
     * @param string $expected
     * @dataProvider providerNamed
     */
    public function testNamed($pattern, $data, $expected)
    {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }

    /**
     * @return array
     */
    public function providerNamed()
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

    /**
     * Creates a templater instance
     *
     * @param string $pattern
     * @param array $data
     * @param string $prefix
     * @return \go\DB\Helpers\Templater
     */
    protected function createTemplater($pattern, $data, $prefix = null)
    {
        $connector = new Connector('test', array('host' => 'localhost'));
        $connector->connect();
        return new Templater($connector, $pattern, $data, $prefix);
    }
}
