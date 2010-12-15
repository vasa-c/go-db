<?php
/**
 * Подключалка к базе
 *
 * Занимается подключением к БД
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
            throw new \go\DB\Exceptions\ConfigConnect();
        }
        $this->implementation = $implementation;
        $this->params         = $params;
    }

    /**
     * Попытаться подключиться, если ещё не подключены
     *
     * @throws \go\DB\Exceptions\Connect
     *         ошибка при подключении
     *
     * @return bool
     *         было ли подключение установлено именно в этот раз
     */
    public function connect() {
        if ($this->connected) {
            return false;
        }
        if (!$this->implementation->connect($this->params)) {
            $error     = $this->implementation->getErrorInfo();
            $errorcode = $this->implementation->getErrorCode();
            throw new \go\DB\Exceptions\Connect($error, $errorcode);
        }
        $this->connected = true;
        return true;
    }

    /**
     * Отключиться, если отключены
     *
     * @return bool
     *         было ли подключение разорвано именно в этот раз
     */
    public function close() {
        if (!$this->connected) {
            return false;
        }
        $this->implementation->close();
        $this->connected = false;
        return true;
    }

    /**
     * Установлено ли подключение
     *
     * @return bool
     */
    public function isConnected() {
        return $this->connected;
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
     * Произведено ли подключение
     *
     * @var bool
     */
    private $connected = false;
}