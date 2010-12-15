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

    }

    /**
     * Отключиться, если отключены
     *
     * @return bool
     *         было ли подключение разорвано именно в этот раз
     */
    public function close() {

    }

    /**
     * Установлено ли подключение
     *
     * @return bool
     */
    public function isConnected() {
        
    }
}