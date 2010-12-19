<?php
/**
 * Итераторы в качестве результата
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers\Iterators;

abstract class Base implements \Iterator, \Countable
{
    /**
     * Конструктор
     *
     * @param \go\DB\Helpers\Connector $connector
     *        подключалка (подключение должно быть установлено)
     * @param mixed $cursor
     *        низкоуровневый курсор
     * @param string $key [optional]
     *        поле используемое в качестве ключа (по умолчанию - порядковый массив)
     */
    public function __construct(\go\DB\Helpers\Connector $connector, $cursor, $key = null) {
        $this->implementation = $connector->getImplementation();
        $this->connection     = $connector->getConnection();
        $this->cursor         = $cursor;
        $this->key            = $key;
        $this->pointer        = 0;
    }

    /**
     * @override \Iterator
     *
     * @return mixed
     */
    public function current() {
        return $this->nextRow;
    }

    /**
     * @override \Iterator
     *
     * @return mixed
     */
    public function key() {
        if (!$this->nextRow) {
            return false;
        }
        if (!is_null($this->key)) {
            return $this->nextRow[$this->key];
        }
        return $this->pointer;
    }

    /**
     * @override \Iterator
     */
    public function next() {
        $this->nextRow = $this->fetchNextRow();
        $this->pointer++;
    }

    /**
     * @override \Iterator
     */
    public function rewind() {
        $this->implementation->rewindCursor($this->connection, $this->cursor);
        $this->pointer = 0;
        $this->nextRow = $this->fetchNextRow();
    }

    /**
     * @override \Iterator
     */
    public function valid() {
        return ($this->nextRow !== false);
    }

    /**
     * @overrider \Countable
     */
    public function count() {
        return $this->implementation->getNumRows($this->connection, $cursor);
    }

    /**
     * Извлечь очередную строку из выборки
     *
     * @return mixed | false
     */
    abstract protected function fetchNextRow();

    /**
     * Внутренняя реализация взаимодействия с базой
     * 
     * @var \go\DB\Implementations\Base
     */
    protected $implementation;

    /**
     * Низкоуровневое подключение к базе
     * 
     * @var mixed
     */
    protected $connection;

    /**
     * Низкоуровневый курсор
     *
     * @var mixed
     */
    protected $cursor;

    /**
     * Поле, используемое в качестве ключа
     *
     * @var string | null
     */
    protected $key;

    /**
     * Указатель на текущую позицию
     * 
     * @var string
     */
    protected $pointer;

    /**
     * Следующая строка в последовательности
     *
     * @var mixed
     */
    protected $nextRow;
}