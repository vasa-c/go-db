<?php
/**
 * Базовый класс тестов
 * Все тесты должны наследоваться от него
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB;

abstract class Base extends \PHPUnit_Framework_TestCase
{
    /**
     * Тестируемый адаптер
     *
     * Если указан - проверяется конфигурация
     * Конфигурация пустая - тест пропускается
     *
     * @var string
     */
    protected $adapterTest;

    /**
     * Имя конфигурации для адаптера
     * Не указана - совпадает с именем адаптера
     *
     * @var string
     */
    protected $adapterConfigName;

    protected function setUp() {
        if ($this->adapterTest) {
            if (!$this->getConfigForAdapter()) {
                $this->markTestSkipped();
            }
        }
        return true;
    }

    /**
     * Получить конфигурацию для тестируемого адаптера
     *
     * @return array | null
     */
    protected function getConfigForAdapter() {
        $name   = $this->adapterConfigName ?: $this->adapterTest;
        $config = Config::getInstance();
        if (!isset($config->$name)) {
            return null;
        }
        return $config->$name;
    }

}