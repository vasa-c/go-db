<?php
/**
 * Тестирование адаптеров
 *
 * Тестирование основных механизмов производится в тестах соответствующих хелперах.
 * Здесь просто немного сборных тестов, для лучшей уверенности.
 *
 * От Base наследуются тесты конкретных адаптеров.
 * Каждый использует хелпер (из _helpers) для создания и наполнения тестовых таблиц.
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB\Adapters;

require_once(__DIR__.'/../../Tests.php');

abstract class Base extends \go\Tests\DB\Base
{
	protected $PATTERN_SHOW_TABLES  =  'SHOW TABLES';

    public function setUp() {
        parent::setUp();
        if (!$this->getHelper()->getConfig()) {
            $this->markTestSkipped();
        }
    }

    public function testCount() {
        $helper = $this->getHelper();
        $db = $helper->getDB('fill');
        $this->assertEquals(5, $db->query('SELECT COUNT(*) FROM {test_table}')->el());
        $this->assertEquals(5, $db->query('SELECT COUNT(*) FROM {test_vars}')->el());        
    }

    public function testInsert() {
        $helper = $this->getHelper();
        $db = $helper->getDB('fill');
        $helper->updated();

        $pattern = 'INSERT INTO ?t (?c, ?c) VALUES (?,?)';
        $data    = array('test_table', 'name', 'number', 'test', 77);
        $this->assertEquals(6, $db->query($pattern, $data, 'id'));
        $data    = array('test_table', 'name', 'number', 'test2', 78);
        $this->assertEquals(7, $db->query($pattern, $data, 'id'));
        $this->assertEquals(7, $db->query('SELECT COUNT(*) FROM {test_table}')->el());
        $name = $db->query('SELECT ?c FROM {test_table} WHERE ?c=?i', array('name', 'id', 6), 'el');
        $this->assertEquals('test', $name);        
    }

    public function testUpdate() {
        $helper = $this->getHelper();
		$helper->dropped();
        $db = $helper->getDB('fill');
        $helper->updated();

        $two = $db->query('SELECT ?c FROM {test_table} WHERE ?c=?i', array('name', 'id', 2))->el();
        $this->assertEquals('two', $two);

        $ar = $db->query('UPDATE {test_table} SET ?c=? WHERE ?c IS NULL', array('name', 'zzz', 'number'))->ar();
        $this->assertEquals(2, $ar);

        $two = $db->query('SELECT ?c FROM {test_table} WHERE ?c=?i', array('name', 'id', 2))->el();
        $this->assertEquals('zzz', $two);
    }

    public function testDrop() {
        $helper = $this->getHelper();
        $db = $helper->getDB(true);
        $helper->dropped();

        $tables = $db->query($this->PATTERN_SHOW_TABLES)->col();
        $this->assertContains('test_table', $tables);
        $this->assertContains('test_vars', $tables);

        $db->query('DROP TABLE IF EXISTS ?t', array('test_table'));

        $tables = $db->query($this->PATTERN_SHOW_TABLES)->col();
        $this->assertNotContains('test_table', $tables);
        $this->assertContains('test_vars', $tables);
   }

    public function testJoinAndPrefix() {
        $helper = $this->getHelper();
        $db = $helper->getDB('fill');

        $db->setPrefix('test_');

        $pattern = 'SELECT ?c FROM {table} LEFT JOIN ?t ON ?c=?c WHERE ?c=?i';
        $data    = array('value', 'vars', 'name', 'key', 'id', 3);
        $this->assertEquals(33, $db->query($pattern, $data, 'el'));
    }

    /**
     * Получить хелпер создания базы
     * 
     * @return \go\Tests\DB\Adapters\_helpers\Base
     */
    protected function getHelper() {
        if (!$this->helper) {
            if (!\class_exists('\go\Tests\DB\Adapters\_helpers\Base', false)) {
                require_once(__DIR__.'/_helpers/Base.php');
            }
            $this->helper = _helpers\Base::getHelperForAdapter($this->getAdapter());
        }
        return $this->helper;
    }

    /**
     * Получить имя тестируемого адаптера
     *
     * @return string
     */
    protected function getAdapter() {
        if (!$this->adapter) {
            \preg_match('~\\\\([^\\\\]+)Test$~s', \get_class($this), $matches);
            $this->adapter = $matches[1];
        }
        return $this->adapter;
    }

    /**
     * Хелпер создания базы
     * (доступ только через getHelper)
     *
     * @var \go\Tests\DB\Adapters\_helpers\Base
     */
    protected $helper;

    /**
     * Тестируемый адаптер
     * (доступ только через getAdapter)
     *
     * @var string
     */
    protected $adapter;
}