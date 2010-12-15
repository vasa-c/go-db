<?php
/**
 * "Движок" тестовой базы
 *
 * Тестовая база принимает три вида запроса:
 *
 * SELECT <col> FROM <table> [LIMIT i,i]
 *
 * <col> - * или через запятую
 * <table> - единственная существующая таблица: "table"
 * 
 * Все "данные" описаны в $table
 *
 * INSERT
 * просто увеличивает автоинкремент. Ничего в "данные" не добавляет
 * автоикремент каждый раз начинается с начала
 *
 * UPDATE [LIMIT i,i]
 * затрагивает affected rows
 *
 * @package    go\DB
 * @subpackage Implementations
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Implementations\TestBase;

final class Engine
{

    /**
     * Тестовая таблица
     * 
     * @var array
     */
    protected $table = array(
        array('a' => 1, 'b' => 2, 'c' => 3),
        array('a' => 2, 'b' => 3, 'c' => 4),
        array('a' => 3, 'b' => 4, 'c' => 5),
        array('a' => 4, 'b' => 4, 'c' => 6),
        array('a' => 5, 'b' => 4, 'c' => 7),
        array('a' => 6, 'b' => 4, 'c' => 8),
    );

    const ERROR_CONNECT  = 1;
    const ERROR_OPERATOR = 2;
    const ERROR_TABLE    = 3;
    const ERROR_COL      = 4;

    /**
     * Выполнения запроса
     *
     * @param string $query
     * @return \go\DB\Implementations\TestBase\Cursor
     */
    public function query($query) {
        
    }

    /**
     * @return string
     */
    public function getErrorInfo() {

    }

    /**
     * @return int
     */
    public function getErrorCode() {
        
    }

    /**
     * @return int
     */
    public function getInsertId() {

    }

    /**
     * @return int
     */
    public function getAffectedRow() {
        
    }
    
}