<?php
/**
 * Хелпер создания тестовых таблиц (адаптер sqlite)
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB\Adapters\_helpers;

class sqlite extends Base
{
    protected $testTypes = array(
        'ID'     => 'INT UNSIGNED NOT NULL',
        'STRING' => 'VARCHAR(200) NULL DEFAULT NULL',
        'INT'    => 'INT NULL DEFAULT NULL',
    );

    protected $testKeys = array(
        'primary' => 'PRIMARY KEY (?cols)',
    );

    protected function fillSingleTestTable($table, array $data) {
        $this->truncateSingleTestTable($table);
        foreach ($data as $d) {
            $pattern = 'INSERT INTO ?table VALUES (?list-null)';
            $this->db->query($pattern, array($table, $d));
        }        
        return true;
    }

    protected function truncateSingleTestTable($table) {
        $this->db->query('DELETE FROM ?table', array($table));
        return true;
    }

    protected function toFill($fill) {
        if ($fill) {
            $this->db->query('BEGIN');
            parent::toFill($fill);
            $this->db->query('COMMIT');
        }
        return true;
    }
}