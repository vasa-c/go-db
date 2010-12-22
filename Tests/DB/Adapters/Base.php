<?php
/**
 * Тестирование адаптеров
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB\Adapters;

require_once(__DIR__.'/../../Tests.php');

abstract class Base extends \go\Tests\DB\Base
{

    public function setUp() {
        parent::setUp();
        if (!$this->getHelper()->getConfig()) {
            $this->markTestSkipped();
        }
    }

    public function testCountAndInsert() {
        $db = $this->getHelper()->getDB('fill');
        $this->assertEquals(5, $db->query('SELECT COUNT(*) FROM {test_table}')->el());
        $this->assertEquals(5, $db->query('SELECT COUNT(*) FROM {test_vars}')->el());

    }

    public function testUpdate() {

    }

    public function testDrop() {

    }

    public function testSelect() {

    }

    public function testJoin() {

    }


    public function testPrefix() {

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