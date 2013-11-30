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
     * @param string $fetch
     *        формат разбора
     * @return mixed
     *         результат в заданном формате
     * @throws \go\DB\Exceptions\Fetch
     *         ошибка при разборе
     */
    public function fetch($fetch);

    /**
     * Очистить результат
     */
    public function free();

    /**
     * Массив ассоциативных массивов
     *
     * @param string $param [optional]
     *        поле, используемое в качестве индекса
     *        не указано - порядковый массив
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function assoc($param = null);

    /**
     * Массив порядковых массивов
     *
     * @param int $param [optional]
     *        номер поля, используемого в качестве индекса
     *        не указано - порядковый массив
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function numerics($param = null);

    /**
     * Массив объектов
     *
     * @param string $param [optional]
     *        поле, используемое в качестве индекса
     *        не указано - порядковый массив
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function objects($param = null);

    /**
     * Массив значений
     *
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function col($param = null);

    /**
     * Список переменных (первое поле - имя переенной, второе - значение)
     *
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function vars($param = null);

    /**
     * Итератор по assoc
     *
     * @param string $param [optional]
     * @return Iterator
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function iassoc($param = null);

    /**
     * Итератор по numerics
     *
     * @param int $param [optional]
     * @return Iterator
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function inumerics($param = null);

    /**
     * Итератор по objects
     *
     * @param string $param [optional]
     * @return Iterator
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function iobjects($param = null);

    /**
     * Итератор по vars
     *
     * @return Iterator
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function ivars($param = null);

    /**
     * Итератор по col
     *
     * @return Iterator
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function icol($param = null);

    /**
     * Ассоциативный массив по одной строке (нет строки - NULL)
     *
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function row($param = null);

    /**
     * Порядковый массив по одной строке (нет строки - NULL)
     *
     * @return array
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function numeric($param = null);

    /**
     * Одна строка в виде объекта (нет строки - NULL)
     *
     * @return object
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function object($param = null);

    /**
     * Одно значение из строки (нет строки - NULL)
     *
     * @return string
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function el($param = null);

    /**
     * Одно значение из строки в виде bool (нет строки - NULL)
     *
     * @return bool
     * @throws \go\DB\Exceptions\UnexpectedFetch
     */
    public function bool($param = null);

    /**
     * Количество строк в результате
     *
     * @return int
     * @throws \go\DB\Exceptions\UnexpectedFetch
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
