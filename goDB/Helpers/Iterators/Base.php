<?php
/**
 * Итераторы в качестве результата
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers\Iterators;

use go\DB\Implementations\Base as Implementation;

abstract class Base implements \Iterator, \Countable
{
    /**
     * Конструктор
     *
     * @param \go\DB\Implementations\Base $implementation
     *        низкоуровневая реализация базы
     * @param mixed $cursor
     *        низкоуровневый курсор
     * @param string $key [optional]
     *        поле используемое в качестве ключа (по умолчанию - порядковый массив)
     */
    public function __construct(Implementation $implementation, $cursor, $key = null) {
        $this->implementation = $implementation;
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
        $this->implementation->rewindCursor($this->cursor);
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
        return $this->implementation->getNumRows($cursor);
    }

    /**
     * Извлечь очередную строку из выборки
     *
     * @return mixed | false
     */
    abstract protected function fetchNextRow();

    /**
     * Низкоуровневая реализация базы
     * 
     * @var \go\DB\Implementations\Base
     */
    protected $implementation;

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