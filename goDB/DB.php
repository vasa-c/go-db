<?php
/**
 * goDB2: библиотека для работы с реляционными базами данных из PHP
 *
 * @package go\DB
 * @link    http://code.google.com/p/go-db/
 * @version 2.0.0 alfa
 * @author  Григорьев Олег aka vasa_c (http://blgo.ru/)
 * @license MIT (http://www.opensource.org/licenses/mit-license.php)
 * @uses    PHP >= 5.3
 * @uses    для каждого адаптера свои расширения
 */

namespace go\DB;

const VERSION = '2.0.0 alfa';

abstract class DB
{

/*** STATIC: ***/

    /**
     * Создать объект для доступа к базе
     *
     * @param array $params
     *        параметры подключения к базе
     * @param string $adapter [optional]
     *        адаптер базы (если не указан в $params)
     * @return \go\DB\DB
     *         объект для доступа к базе
     */
    final public static function create(array $params, $adapter = null) {

    }

    /**
     * Получить список доступных адаптеров
     *
     * @return array
     */
    final public static function getAvailableAdapters() {
        
    }

/*** PUBLIC: ***/

    /**
     * Выполнить запрос
     *
     * @param string $pattern
     *        шаблон запроса
     * @param array $data [optional]
     *        входящие данные для запроса
     * @param string $fetch [optional]
     *        формат представления результата
     * @param string $prefix [optional]
     *        префикс таблиц для данного конкретного запроса
     * @return mixed
     *         результат в заданном формате
     */
    final public function query($pattern, $data = null, $fetch = null, $prefix = null) {
        
    }

    /**
     * Выполнение "чистого" запроса
     *
     * @param string $query
     *        SQL-запрос
     * @param string $fetch [optional]
     *        формат представления результата
     * @return mixed
     *         результат в заданном формате
     */
    final public function plainQuery($query, $fetch = null) {

    }

    /**
     * Вызов объекта, как функции - переадресация на query()
     * 
     * Следующие два примера идентичны:
     * @example $db->query('SELECT * FROM `table`');
     * @example $db('SELECT * FROM `table`');
     * 
     * @param string $pattern
     * @param array $data [optional]
     * @param string $fetch [optional]
     * @param string $prefix [optional]
     * @return mixed
     */
    final public function __invoke($pattern, $data = null, $fetch = null, $prefix = null) {

    }

    /**
     * Установлено ли соединение фактически
     *
     * @return bool
     */
    final public function isConnected() {

    }

    /**
     * Принудительно установить соединение, если оно ещё не установлено
     */
    final public function forcedConnect() {

    }

    /**
     * Закрыть соединение
     *
     * @param bool $safe [optional]
     *        "мягкое" закрытие: с возможностью восстановления
     */
    final public function close($safe = false) {

    }

    /**
     * Закрыто ли соединение "жестким" образом
     *
     * @return bool
     */
    final public function isClosed() {

    }

    /**
     * Установить префикс таблиц
     *
     * @param string $prefix
     */
    final public function setPrefix($prefix) {

    }

    /**
     * Получить префикс таблиц
     *
     * @return string
     */
    final public function getPrefix() {

    }

    /**
     * Установить обработчик отладочной информации
     *
     * @param callback $callback
     *        обработчик (true - стандартный)
     */
    final public function setDebug($callback = true) {

    }

    /**
     * Получить обработчик отладочной информации
     *
     * @return callback
     */
    final public function getDebug() {

    }

    /**
     * Отключить отправку отладочной информации
     */
    final public function disableDebug() {

    }

    /**
     * Получить внутреннюю реализацию подключения к базе
     *
     * @return mixed
     */
    final public function getImplementationConnection() {
        
    }

/*** PROTECTED: ***/

/*** VARS: ***/


}