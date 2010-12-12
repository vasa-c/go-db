<?php
/**
 * Интерфейс объектов, возвращаемых в виде результата запроса
 *
 * @example $result = $db->query($pattern, $data);
 * Объект $result инкапсулирует в себе конкретную реализацию курсора.
 * Например, для mysql-драйвера это может быть mysqli_result-объект.
 *
 * @package go\DB
 * @author  Григорьев Олег aka vasa_c
 */

namespace go\DB;

interface Result extends \IteratorAggregate
{
    /**
     * Разобрать результат в соответствии с форматом
     *
     * @throws \go\DB\Exceptions\Fetch
     *         ошибка при разборе
     *
     * @param string $fetch
     *        формат разбора
     * @return mixed
     *         результат в заданном формате
     */
    public function fetch($fetch);

    /**
     * Очистить результат
     */
    public function free();

    /**
     * Массив ассоциативных массивов
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param string $param [optional]
     *        поле, используемое в качестве индекса
     *        не указано - порядковый массив
     * @return array
     */
    public function assoc($param = null);

    /**
     * Массив порядковых массивов
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param int $param [optional]
     *        номер поля, используемого в качестве индекса
     *        не указано - порядковый массив
     * @return array
     */
    public function numerics($param = null);

    /**
     * Массив объектов
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param string $param [optional]
     *        поле, используемое в качестве индекса
     *        не указано - порядковый массив
     * @return array
     */
    public function objects($param = null);

    /**
     * Массив значений
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return array
     */
    public function col($param = null);

    /**
     * Список переменных (первое поле - имя переенной, второе - значение)
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return array
     */
    public function vars($param = null);

    /**
     * Итератор по assoc
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param string $param [optional]
     * @return Iterator
     */
    public function iassoc($param = null);

    /**
     * Итератор по numerics
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param int $param [optional]
     * @return Iterator
     */
    public function inumerics($param = null);

    /**
     * Итератор по objects
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param string $param [optional]
     * @return Iterator
     */
    public function iobjects($param = null);

    /**
     * Итератор по vars
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return Iterator
     */
    public function ivars($param = null);

    /**
     * Итератор по col
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return Iterator
     */
    public function icol($param = null);

    /**
     * Ассоциативный массив по одной строке (нет строки - NULL)
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return array
     */
    public function row($param = null);

    /**
     * Порядковый массив по одной строке (нет строки - NULL)
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return array
     */
    public function numeric($param = null);

    /**
     * Одна строка в виде объекта (нет строки - NULL)
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return object
     */
    public function object($param = null);

    /**
     * Одно значение из строки (нет строки - NULL)
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return string
     */
    public function el($param = null);

    /**
     * Одно значение из строки в виде bool (нет строки - NULL)
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return bool
     */
    public function bool($param = null);

    /**
     * Количество строк в результате
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return int
     */
    public function num($param = null);

    /**
     * Последний AUTO_INCREMENT
     *
     * @return int
     */
    public function id($param = null);

    /**
     * Количество затронутых запросом строк
     * 
     * @return int
     */
    public function ar($param = null);

    /**
     * Внутренняя реализация результата
     *
     * @return mixed
     */
    public function cursor($param = null);
}