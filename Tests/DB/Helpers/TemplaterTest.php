<?php
/**
 * Тестирование шаблонизатора
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB\Helpers;

require_once(__DIR__.'/../../Tests.php');

use go\DB\Helpers\Templater as Templater;
use go\DB\Implementations\test as Implementation;

/**
 * @covers \go\DB\Helpers\Templater
 */
final class TemplaterTest extends \go\Tests\DB\Base
{
    /**
     * ?, ?string, ?scalar
     * @dataProvider providerScalar
     */
    public function testScalar($pattern, $data, $expected) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }
    public function providerScalar() {
        $data = array('стр"ока', 1, null, '3.5');
        return array(
            array(
                'INSERT INTO `table` VALUES (?, ?scalar, ?, ?string)',
                $data,
                'INSERT INTO `table` VALUES ("стр\"ока", "1", "", "3.5")',
            ),
            array(
                'INSERT INTO `table` VALUES (?null, ?null, ?null, ?null)',
                $data,
                'INSERT INTO `table` VALUES ("стр\"ока", "1", NULL, "3.5")',
            ),
            array(
                'INSERT INTO `table` VALUES (?i, ?i, ?i, ?i)',
                $data,
                'INSERT INTO `table` VALUES (0, 1, 0, 3)',
            ),
            array(
                'INSERT INTO `table` VALUES (?in, ?in, ?in, ?in)',
                $data,
                'INSERT INTO `table` VALUES (0, 1, NULL, 3)',
            ),
            array(
                'INSERT INTO `table` VALUES (?string, ?scalar-int, ?scalar-null, ?scalar-int-null)',
                $data,
                'INSERT INTO `table` VALUES ("стр\"ока", 1, NULL, 3)',
            ),
        );
    }

    /**
     * ?l, ?list
     * @dataProvider providerList
     */
    public function testList($pattern, $data, $expected) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }
    public function providerList() {
        $list = array('стр"ока', 1, null, '3.5');
        return array(
            array(
                'INSERT INTO `table` VALUES (?l)',
                array($list),
                'INSERT INTO `table` VALUES ("стр\"ока", "1", "", "3.5")',
            ),
            array(
                'INSERT INTO `table` VALUES (?list)',
                array($list),
                'INSERT INTO `table` VALUES ("стр\"ока", "1", "", "3.5")',
            ),
            array(
                'INSERT INTO `table` VALUES (?ln)',
                array($list),
                'INSERT INTO `table` VALUES ("стр\"ока", "1", NULL, "3.5")',
            ),
            array(
                'INSERT INTO `table` VALUES (?li)',
                array($list),
                'INSERT INTO `table` VALUES (0, 1, 0, 3)',
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
     * @dataProvider providerSet
     */
    public function testSet($pattern, $data, $expected) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }
    public function providerSet() {
        $set = array(
            's' => 'стр"ока',
            'd' => '3.5',
            'n' => null,
        );
        return array(
            array(
                'INSERT INTO `table` SET ?s',
                array($set),
                'INSERT INTO `table` SET `s`="стр\"ока", `d`="3.5", `n`=""',
            ),
            array(
                'INSERT INTO `table` SET ?set-null',
                array($set),
                'INSERT INTO `table` SET `s`="стр\"ока", `d`="3.5", `n`=NULL',
            ),
            array(
                'INSERT INTO `table` SET ?sin',
                array($set),
                'INSERT INTO `table` SET `s`=0, `d`=3, `n`=NULL',
            ),
        );
    }

    /**
     * ?v, ?values
     * @dataProvider providerValues
     */
    public function testValues($pattern, $data, $expected) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }
    public function providerValues() {
        $values = array(
            array(0, 1, 2),
            array('раз', null, 'три'),
        );
        return array(
            array(
                'INSERT INTO `table` VALUES ?values;',
                array($values),
                'INSERT INTO `table` VALUES ("0", "1", "2"), ("раз", "", "три")',
            ),
            array(
                'INSERT INTO `table` VALUES ?vn',
                array($values),
                'INSERT INTO `table` VALUES ("0", "1", "2"), ("раз", NULL, "три")',
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
     * @dataProvider providerField
     */
    public function testFields($pattern, $data, $expected) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }
    public function providerField() {
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
        );
    }

    /**
     * ?e, ?escape
     * @dataProvider providerEscape
     */
    public function testEscape($pattern, $data, $expected) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }
    public function providerEscape() {
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
     * @dataProvider providerQuery
     */
    public function testQuery($pattern, $data, $expected) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }
    public function providerQuery() {
        return array(
            array(
                'SELECT * FROM ?t ?q ORDER BY ?c DESC',
                array('table', 'WHERE `q`="?"', 'c'),
                'SELECT * FROM `table` WHERE `q`="?" ORDER BY `c` DESC',
            ),
        );
    }

    /**
     * ?? - вставка вопросительного знака
     * @dataProvider providerQMark
     */
    public function testQMark($pattern, $data, $expected) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }
    public function providerQMark() {
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
     * Префикс таблиц
     * @dataProvider providerPrefix
     */
    public function testPrefix($pattern, $data, $prefix, $expected) {
        $templater = $this->createTemplater($pattern, $data, $prefix);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }
    public function providerPrefix() {
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
     * Именованные плейсхолдеры
     * @dataProvider providerNamed
     */
    public function testNamed($pattern, $data, $expected) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
        $this->assertEquals($expected, $templater->getQuery());
    }
    public function providerNamed() {
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
     * @dataProvider providerExceptionUnknownPlaceholder
     * @expectedException \go\DB\Exceptions\UnknownPlaceholder
     */
    public function testExceptionUnknownPlaceholder($pattern, $data) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
    }
    public function providerExceptionUnknownPlaceholder() {
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
     * @dataProvider providerExceptionDataNotEnough
     * @expectedException \go\DB\Exceptions\DataNotEnough
     */
    public function testExceptionDataNotEnough($pattern, $data) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
    }
    public function providerExceptionDataNotEnough() {
        return array(
            array(
                'INSERT INTO `table` VALUES (?,?,?)',
                array(1, 2)
            ),
        );
    }

    /**
     * @dataProvider providerExceptionDataMuch
     * @expectedException \go\DB\Exceptions\DataMuch
     */
    public function testExceptionUnknownDataNotMuch($pattern, $data) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
    }
    public function providerExceptionDataMuch() {
        return array(
            array(
                'INSERT INTO `table` VALUES (?,?,?)',
                array(1, 2, 3, 4)
            ),
        );
    }

    /**
     * @dataProvider providerExceptionDataNamed
     * @expectedException \go\DB\Exceptions\DataNamed
     */
    public function testExceptionUnknownDataNamed($pattern, $data) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
    }
    public function providerExceptionDataNamed() {
        return array(
            array(
                'INSERT INTO `table` VALUES (?:a,?i:b,?:c)',
                array('a' => 1, 'b' => 2)
            ),
        );
    }

    /**
     * @dataProvider providerExceptionDataMixed
     * @expectedException \go\DB\Exceptions\DataNamed
     */
    public function testExceptionUnknownDataMixed($pattern, $data) {
        $templater = $this->createTemplater($pattern, $data);
        $templater->parse();
    }
    public function providerExceptionDataMixed() {
        return array(
            array(
                'INSERT INTO `table` VALUES (?:a,?i:b,?:c)',
                array('a' => 1, 'b' => 2)
            ),
        );
    }

    /**
     * Создать объект шаблонизатора
     *
     * @param string $pattern
     * @param array $data
     * @param string $prefix
     * @return \go\DB\Templaters\Base
     */
    protected function createTemplater($pattern, $data, $prefix = null) {
        $connector = new \go\DB\Helpers\Connector('test', array('host' => 'localhost'));
        $connector->connect();
        return new \go\DB\Helpers\Templater($connector, $pattern, $data, $prefix);
    }
}