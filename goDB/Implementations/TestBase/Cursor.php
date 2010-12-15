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
        
    }

    /**
     * fetch_row
     *
     * @return array | false
     */
    public function fetchRow() {

    }

    /**
     * fetch_assoc
     *
     * @return array | false
     */
    public function fetchAssoc() {

    }

    /**
     * fetch_object
     *
     * @return array | false
     */
    public function fetchObject() {

    }

    /**
     * Сброс курсора
     */
    public function reset() {

    }

    /**
     * Получить количество записей в "выборке"
     *
     * @return int
     */
    public function getNumRows() {
        
    }

}