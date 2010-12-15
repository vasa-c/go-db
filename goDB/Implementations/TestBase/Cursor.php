<?php
/**
 * Курсор тестовой базы
 *
 * @package    go\DB
 * @subpackage Implementations
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Implementations\TestBase;

final class Cursor
{
    /**
     * Конструктор
     *
     * @param array $data
     *        данные "выборки"
     */
    public function __construct(array $data) {
        $this->data = $data;
        $this->reset();
    }

    /**
     * fetch_row
     *
     * @return array | false
     */
    public function fetchRow() {
        $row = $this->next();
        return $row ? array_values($row) : false;
    }

    /**
     * fetch_assoc
     *
     * @return array | false
     */
    public function fetchAssoc() {
        $row = $this->next();
        return $row ? $row : false;
    }

    /**
     * fetch_object
     *
     * @return array | false
     */
    public function fetchObject() {
        $row = $this->next();
        return $row ? (object)$row : false;
    }

    /**
     * Сброс курсора
     */
    public function reset() {
        reset($this->data);
        return true;
    }

    /**
     * Получить количество записей в "выборке"
     *
     * @return int
     */
    public function getNumRows() {
        return count($this->data);
    }

    /**
     * @return array
     */
    private function next() {
        $value = current($this->data);
        next($this->data);
        return $value;
    }

    /**
     * @var array
     */
    private $data;
}