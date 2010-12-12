<?php
/**
 * Исключение: ошибка хранилища баз
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       http://code.google.com/p/go-ns/wiki/go_DB_Exceptions_Storage
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

abstract class Storage extends Logic
{
    /**
     * Шаблон сообщения, переопределяется у потомков
     *
     * @var string
     */
    protected $MESSAGE_PATTERN;

    /**
     * Конструктор
     *
     * @param string $dbname
     *        ошибочное имя базы
     */
    public function __construct($dbname) {
        $message = str_replace(
            '{{ dbname }}',
            $dbname,
            $this->MESSAGE_PATTERN
        );
        $this->dbname = $dbname;
        parent::__construct($message);
    }

    /**
     * Получить ошибочное название базы
     *
     * @return string
     */
    public function getDBName() {
        return $this->dbname;
    }

    protected $dbname;
}