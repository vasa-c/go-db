<?php
/**
 * Абстракция над конкретной реализацией доступа к базе
 *
 * Например, если адаптер MySQL является надстройкой над php_mysql, то
 * для этого наследуется класс от Base, который делегирует вызовы mysql_* функциям.
 *
 * Все публичные методы данного класса (кроме getCursor) теоретически абстрактные
 * и подлежат переопределению в потомках, но некоторые функции уже определены, так как
 * их поведение одинаково для большинства драйверов.
 *
 * @package    go\DB
 * @subpackage Implementations
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Implementations;

abstract class Base
{
    /**
     * Получить внутреннюю реализацию подключения
     *
     * @return mixed
     */
    final public function getConnection() {
        return $this->connection;
    }

    /**
     * Подключение к серверу БД и выбор базы
     *
     * @param array $params
     *        параметры подключения
     * @return bool
     *         успешно или нет
     */
    abstract public function connect($params);

    /**
     * Закрыть подключение к серверу
     */
    abstract public function close();

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
     * @param mixed $cursor
     * @return bool
     */
    public function isCursor($result) {
        return ($result !== true);
    }

    /**
     * Выполнение запроса к базе
     *
     * @param string $query
     *        SQL-запрос
     * @return mixed
     *         курсор результата для выборки, TRUE для других запросов, FALSE - ошибка
     */
    abstract public function query($query);

    /**
     * Получить последний авто-инкремент (или аналог для баз без него)
     *
     * @return int
     */
    abstract public function getInsertId();

    /**
     * Получить количество строк, затронутых запросом
     *
     * @return int
     */
    abstract public function getAffectedRows();

    /**
     * Получить описание последней ошибки
     *
     * @return string
     */
    public function getErrorInfo() {
        if ($this->connection) {
            return $this->realErrorInfo();
        }
        return $this->errorInfo;
    }

    /**
     * Получить описание последей ошибки с использованием $connection
     *
     * @return string
     */
    abstract protected function realErrorInfo();

    /**
     * Получить код последней ошибки
     *
     * @return int
     */
    public function getErrorCode() {
        if ($this->connection) {
            return $this->realErrorCode();
        }
        return $this->errorCode;
    }

    /**
     * Получить код последней ошибки с использованием $connection
     *
     * @return int
     */
    abstract protected function realErrorCode();

    /**
     * Получить количество строк в выборке
     *
     * @param mixed $cursor
     * @return int
     */
    abstract public function getNumRows($cursor);

    /**
     * Получить очередную строку в виде порядкового массива
     *
     * @param mixed $cursor
     * @return array|false
     */
    abstract public function fetchRow($cursor);

    /**
     * Получить очередную строку в виде ассоциативного массива
     *
     * @param mixed $cursor
     * @return array|false
     */
    abstract public function fetchAssoc($cursor);

    /**
     * Получить очередную строку в виде объекта
     *
     * @param mixed $cursor
     * @return object|false
     */
    public function fetchObject($cursor) {
        $result = $this->fetchAssoc($cursor);
        return $result ? (object)$result : false;
    }

    /**
     * Освободить курсор
     *
     * @param mixed $cursor
     */
    abstract public function freeCursor($cursor);

    /**
     * Экранирование спецсимволов в строке
     *
     * @param scalar $value
     * @return string
     */
    public function escapeString($value) {
        return \addslashes($value);
    }

    /**
     * Представление строки, как данного
     *
     * @param scalar $value
     * @return string
     */
    public function reprString($value) {
        return '"'.$this->escapeString($value).'"';
    }

    /**
     * Представление целого числа, как данного
     * 
     * @param scalar $value
     * @return string
     */
    public function reprInt($value) {
        return (int)$value;
    }

    /**
     * Представление вещественного числа, как данного
     * 
     * @param scalar $value
     * @return string
     */
    public function reprFloat($value) {
        return (0 + $value);
    }

    /**
     * Представление логического значения
     * 
     * @param scalar $value
     * @return string
     */
    public function reprBool($value) {
        return $value ? '1' : '0';
    }

    /**
     * Представление NULL'а
     *
     * @return string
     */
    public function reprNULL() {
        return 'NULL';
    }

    /**
     * Представление таблицы
     *
     * @param string $value
     * @return string
     */
    public function reprTable($value) {
        return $this->reprField($value);
    }

    /**
     * Представление столбца
     * 
     * @param string $value
     * @return string
     */
    public function reprCol($value) {
        return $this->reprField($value);
    }

    /**
     * Представление цепочки полей
     * Например: db.table.col
     *
     * @param array $fields
     * @return string
     */
    public function reprChainFields(array $fields) {
        $result = array();
        foreach ($fields as $field) {
            $result[] = $this->reprField($field);
        }
        return implode('.', $result);
    }

    /**
     * Представление поля (таблицы или столбца)
     *
     * @param string $value
     * @return string
     */
    protected function reprField($value) {
        return '"'.$value.'"';
    }

    /**
     * Вернуться в начало курсора
     * 
     * @param mixed $cursor
     */
    abstract public function rewindCursor($cursor);

    /**
     * Внутренняя реализация подключения, зависящая от адаптера
     *
     * @var mixed
     */
    protected $connection;

    /**
     * Обязательные параметров подключения
     *
     * @var array
     */
    protected $paramsReq = array();

    /**
     * Необязательные параметры подключения
     *
     * параметр => значение по умолчанию
     *
     * @var array
     */
    protected $paramsDefault = array();

    /**
     * Сохранённое (если надо) сообщение об ошибке
     *
     * @var string
     */
    protected $errorInfo;

    /**
     * Сохранённый (если надо) код ошибки
     *
     * @var string
     */
    protected $errorCode;
}