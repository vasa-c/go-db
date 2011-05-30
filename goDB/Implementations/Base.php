<?php
/**
 * Абстракция над конкретной низкоуровневой реализацией доступа к базе
 *
 * Например, если адаптер MySQL является надстройкой над php_mysql, то
 * для этого наследуется класс от Base, который делегирует вызовы mysql_* функциям.
 *
 * Объект данного класса не содержит никаких индивидуальных свойств.
 * Переменные подключения и курсора передаются в методы в виде аргументов.
 *
 * Все публичные методы данного класса теоретически абстрактные
 * и подлежат переопределению в потомках, но некоторые функции уже определены, так как
 * их поведение одинаково для большинства драйверов.
 *
 * Никаких исключений данные методы не выбрасывают. В случае ошибки возвращается FALSE.
 *
 * @package    go\DB
 * @subpackage Implementations
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Implementations;

abstract class Base
{
    /**
     * Обязательные параметры подключения
     * (переопределяются у потомков)
     *
     * @var array
     */
    protected $paramsReq = array();

    /**
     * Необязательные параметры подключения
     * (переопределяются у потомков)
     *
     * параметр => значение по умолчанию
     *
     * @var array
     */
    protected $paramsDefault = array();

    /**
     * Получить объект реализации для конкретного адаптера
     * 
     * @param string $adapter
     *        адаптер
     * @return \go\DB\Implementations\Base
     */
    public static function getImplementationForAdapter($adapter) {
        if (!isset(self::$cacheAdapters[$adapter])) {
            $classname = __NAMESPACE__.'\\'.$adapter;
            self::$cacheAdapters[$adapter] = new $classname();
        }
        return self::$cacheAdapters[$adapter];
    }

    /**
     * Закрытый конструктор - создание только через getImplementationForAdapter()
     */
    protected function __construct() {
    }

    /**
     * Подключение к серверу БД и выбор базы
     *
     * Так как при ошибке не возвращается подключение, нет возможности
     * узнать информацию об ошибках через getErrorInfo() и getErrrorCode().
     * Поэтому для этого используются аргументы по ссылке
     *
     * @param array $params
     *        параметры подключения
     * @param string & $errroInfo
     * @param int & $errorCode
     * @return mixed
     *         реализация подключения или FALSE при ошибке
     */
    abstract public function connect(array $params, &$errorInfo = null, &$errorCode = null);

    /**
     * Закрыть подключение к серверу
     *
     * @param mixed $connection
     */
    abstract public function close($connection);

    /**
     * Проверка структуры параметров подключения
     * 
     * @param array $params
     *        параметры подключения
     * @return array
     *         нормализовнные параметры или FALSE при некорректной структуре
     */
    public function checkParams(array $params) {
        $result = array();
        foreach ($this->paramsReq as $param) {
            if (!array_key_exists($param, $params)) {
                return false;
            }
            $result[$param] = $params[$param];
        }
        foreach ($this->paramsDefault as $param => $default) {
            if (array_key_exists($param, $params)) {
                $result[$param] = $params[$param];
            } else {
                $result[$param] = $default;
            }
        }
        return $result;
    }

    /**
     * Является ли результат запроса курсором (результатом выборки)
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return bool
     */
    public function isCursor($connection, $result) {
        return ($result !== true);
    }

    /**
     * Выполнение запроса к базе
     *
     * @param mixed $connection
     * @param string $query
     *        SQL-запрос
     * @return mixed
     *         курсор результата для выборки, TRUE для других запросов, FALSE - ошибка
     */
    abstract public function query($connection, $query);

    /**
     * Получить последний авто-инкремент (или аналог для баз без него)
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    abstract public function getInsertId($connection, $cursor = null);

    /**
     * Получить количество строк, затронутых запросом
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    abstract public function getAffectedRows($connection, $cursor = null);

    /**
     * Получить описание последней ошибки
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return string
     */
    abstract public function getErrorInfo($connection, $cursor = null);


    /**
     * Получить код последней ошибки
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    abstract public function getErrorCode($connection, $cursor);


    /**
     * Получить количество строк в выборке
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return int
     */
    abstract public function getNumRows($connection, $cursor);

    /**
     * Получить очередную строку в виде порядкового массива
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    abstract public function fetchRow($connection, $cursor);

    /**
     * Получить очередную строку в виде ассоциативного массива
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    abstract public function fetchAssoc($connection, $cursor);

    /**
     * Получить очередную строку в виде объекта
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return object|false
     */
    public function fetchObject($connection, $cursor) {
        $result = $this->fetchAssoc($connection, $cursor);
        return $result ? (object)$result : false;
    }

    /**
     * Освободить курсор
     *
     * @param mixed $connection
     * @param mixed $cursor
     */
    abstract public function freeCursor($connection, $cursor);

    /**
     * Экранирование спецсимволов в строке
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function escapeString($connection, $value) {
        return \addslashes($value);
    }

    /**
     * Представление строки, как данного
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function reprString($connection, $value) {
        return '"'.$this->escapeString($connection, $value).'"';
    }

    /**
     * Представление целого числа, как данного
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function reprInt($connection, $value) {
        $value = (int)$value;
        if ($value < 0) {
            $value = '('.$value.')';
        }
        return $value;
    }

    /**
     * Представление вещественного числа, как данного
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function reprFloat($connection, $value) {
        return (0 + $value);
    }

    /**
     * Представление логического значения
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function reprBool($connection, $value) {
        return $value ? '1' : '0';
    }

    /**
     * Представление NULL'а
     *
     * @param mixed $connection
     * @return string
     */
    public function reprNULL($connection) {
        return 'NULL';
    }

    /**
     * Представление таблицы
     *
     * @param mixed $connection
     * @param string $value
     * @return string
     */
    public function reprTable($connection, $value) {
        return $this->reprField($connection, $value);
    }

    /**
     * Представление столбца
     *
     * @param mixed $connection
     * @param string $value
     * @return string
     */
    public function reprCol($connection, $value) {
        return $this->reprField($connection, $value);
    }

    /**
     * Представление цепочки полей
     * Например: db.table.col
     *
     * @param mixed $connection
     * @param array $fields
     * @return string
     */
    public function reprChainFields($connection, array $fields) {
        $result = array();
        foreach ($fields as $field) {
            $result[] = $this->reprField($connection, $field);
        }
        return implode('.', $result);
    }

    /**
     * Представление поля (таблицы или столбца)
     *
     * @param mixed $connection
     * @param string $value
     * @return string
     */
    protected function reprField($connection, $value) {
        return '"'.$value.'"';
    }

    /**
     * Вернуться в начало курсора
     *
     * @param mixed $connection
     * @param mixed $cursor
     */
    abstract public function rewindCursor($connection, $cursor);

    /**
     * Кэш имплементаторов по адаптерам
     * 
     * @var array
     */
    private static $cacheAdapters = array();
}