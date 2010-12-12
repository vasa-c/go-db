<?php
/**
 * Доступ к тестовой конфигурации
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB;

final class Config {

    /**
     * Получить экземпляр конфига
     *
     * @return \go\Tests\DB\Config
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self(__DIR__.'/_config');
        }
        return self::$instance;
    }

    /**
     * Доступ к параметру конфигурации
     *
     * @throws \RuntimeException
     *         нет такого параметра
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if (\array_key_exists($name, $this->config)) {
            return $this->config[$name];
        }
        $filename = $this->getFilename($name);
        if (!\file_exists($filename)) {
            throw new \RuntimeException('Error test config param "'.$name.'"');
        }
        return include($filename);
    }

    /**
     * Существует ли заданный параметр конфигурации
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        if (\array_key_exists($name, $this->config)) {
            return true;
        }
        $filename = $this->getFilename($name);
        if (!\file_exists($filename)) {
            return false;
        }
        return true;
    }

    /**
     * Скрытый конструктор - извне не создать
     *
     * @param string $dir
     *        каталог с конфигурацией
     */
    private function __construct($dir) {
        $this->dir = $dir;
    }

    /**
     * Получить имя файла, содержащего параметр конфигурации
     * 
     * @param string $name
     *        имя параметра
     * @return string
     *         имя файла
     */
    private function getFilename($name) {
        return $this->dir.'/'.$name.'.php';
    }

    /**
     * Экземпляр конфигурации
     *
     * @var \go\Tests\DB\Config
     */
    private static $instance;

    /**
     * Уже загруженные параметры конфига
     *
     * @var array
     */
    private $config = array();

    /**
     * Каталог с конфигурацией
     *
     * @return string
     */
    private $dir;
}