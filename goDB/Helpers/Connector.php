<?php
/**
 * Подключалка к базе
 *
 * Занимается подключением к БД.
 * Агрегирует в себе объект низкоуровневого подключения, агрегируется в объект DB.
 *
 * Может разделяться несколькими объектами DB.
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers;

use go\DB\Exceptions as Exceptions;

final class Connector
{
    /**
     * Конструктор
     *
     * @throws \go\DB\Exceptions\ConfigConnect
     *         невеверный формат параметров подключения
     *
     * @param string $adapter
     *        для какого адаптера
     * @param array $params
     *        параметры подключения
     */
    public function __construct($adapter, array $params) {
        $this->implementation = \go\DB\Implementations\Base::getImplementationForAdapter($adapter);
        $this->params = $this->implementation->checkParams($params);
        if (!$this->params) {
            throw new Exceptions\ConfigConnect();
        }
        $this->countLinks       = 1;
        $this->countConnections = 0;
    }

    /**
     * Деструктор - уничтожение всех подключений
     */
    public function __destruct() {
        $this->deny();
    }

    /**
     * Требование подключения
     *
     * @throws \go\DB\Exceptions\Connect
     *         ошибка при подключении
     *
     * @return bool
     *         было ли подключение установлено именно в этот раз
     */
    public function connect() {
        if ($this->connection) {
            $this->countConnections++;
            return false;
        }
        $connection = $this->implementation->connect($this->params, $errorInfo, $errorCode);
        if (!$connection) {
            throw new Exceptions\Connect($errorInfo, $errorCode);
        }
        $this->connection = $connection;
        $this->countConnections = 1;
        return true;
    }

    /**
     * Требование отключения
     *
     * @return bool
     *         было ли подключение разорвано именно в этот раз
     */
    public function close() {
        if (!$this->connection) {
            return false;
        }
        $this->countConnections--;
        if ($this->countConnections > 0) {
            return false;
        }
        $this->implementation->close($this->connection);
        $this->connection = null;
        return true;
    }

    /**
     * Установлено ли подключение
     *
     * @return bool
     */
    public function isConnected() {
        return (!empty($this->connection));
    }

    /**
     * Добавить ссылку из объекта базы
     *
     * @param bool $connection
     *        есть ли в этой базе уже подключение
     */
    public function addLink($connection) {
        $this->countLinks++;
        if ($connection) {
            $this->countConnections++;
        }
        return true;
    }

    /**
     * Удалить ссылку из объекта базы
     */
    public function removeLink() {
        $this->countLinks--;
        if ($this->countLinks == 0) {
            $this->deny();
        }
        return true;
    }

    /**
     * Узнать количество ссылок на коннектор
     *
     * @return int
     */
    public function getCountConnections() {
        return $this->countConnections;
    }

    /**
     * Получить реализацию подключения
     *
     * @return mixed
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Получить имплементатор
     *
     * @return \go\DB\Implementations\Base
     */
    public function getImplementation() {
        return $this->implementation;
    }

    /**
     * Уничтожение всех подклбчений
     */
    protected function deny() {
        if ($this->connection) {
            $this->implementation->close($this->connection);
            $this->connection = null;
        }
        $this->countLinks       = 0;
        $this->countConnections = 0;
        return true;
    }


    /**
     * Внутренняя реализация базы
     *
     * @var \go\DB\Implementations\Base
     */
    private $implementation;

    /**
     * Низкоуровневая реализация подключения
     *
     * @var mixed
     */
    private $connection;

    /**
     * Параметры подключения
     *
     * @var array
     */
    private $params;

    /**
     * Количество ссылок из различных объектов DB
     *
     * @var int
     */
    private $countLinks;

    /**
     * Количество запрошенных подключений
     *
     * @var int
     */
    private $countConnections;
}