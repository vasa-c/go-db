<?php
/**
 * Подключалка к базе
 *
 * Занимается подключением к БД, агрегируется в объект DB.
 *
 * Может разделяться несколькими объектами DB.
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers;

use go\DB\Implementations\Base as Implementation;
use go\DB\Exceptions as Exceptions;

final class Connector
{
    /**
     * Конструктор
     *
     * @throws \go\DB\Exceptions\ConfigConnect
     *         невеверный формат параметров подключения
     *
     * @param Implementations $implementation
     *        реализация соединения с базой
     * @param array $params
     *        параметры подключения
     */
    public function __construct(Implementation $implementation, array $params) {
        $params = $implementation->checkParams($params);
        if (!$params) {
            throw new Exceptions\ConfigConnect();
        }
        $this->implementation = $implementation;
        $this->params         = $params;
        $this->links          = 1;
        $this->connections    = 0;
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
        $this->connections++;
        if ($this->connections > 1) {
            return false;
        }
        if (!$this->implementation->connect($this->params)) {
            $error     = $this->implementation->getErrorInfo();
            $errorcode = $this->implementation->getErrorCode();
            throw new Exceptions\Connect($error, $errorcode);
        }
        return true;
    }

    /**
     * Отключиться, если отключены
     *
     * @return bool
     *         было ли подключение разорвано именно в этот раз
     */
    public function close() {
        if ($this->connections == 0) {
            return false;
        }
        $this->connections--;
        if ($this->connections > 0) {
            return false;
        }
        $this->implementation->close();
        return true;
    }

    /**
     * Установлено ли подключение
     *
     * @return bool
     */
    public function isConnected() {
        return $this->connections > 0;
    }

    /**
     * Добавить ссылку из объекта базы
     *
     * @param bool $connection
     *        есть ли в этой базе уже подключение
     */
    public function addLink($connection) {
        $this->links++;
        if ($connection) {
            $this->connections++;
        }
        return true;
    }

    /**
     * Удалить ссылку из объекта базы
     */
    public function removeLink() {
        $this->links--;
        if ($this->links < 1) {
            $this->implementation = null;
        }
        return true;
    }

    /**
     * Узнать количество ссылок на коннектор
     *
     * @return int
     */
    public function getCountConnections() {
        return $this->connections;
    }

    /**
     * Внутренняя реализация базы
     *
     * @var \go\DB\Implementations\Base
     */
    private $implementation;

    /**
     * Параметры подключения
     *
     * @var array
     */
    private $params;

    /**
     * Количество ссылок на коннектор из объектов баз
     *
     * @var int
     */
    private $links;

    /**
     * Количество затребованных подключений
     *
     * @var int
     */
    private $connections;
}