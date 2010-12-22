<?php
/**
 * Хелпер создания тестовых таблиц (адаптер mysql)
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB\Adapters\_helpers;

class mysql extends Base
{
    protected $testTypes = array(
        'ID'     => 'INT UNSIGNED NOT NULL AUTO_INCREMENT',
        'STRING' => 'VARCHAR(200) NULL DEFAULT NULL',
        'INT'    => 'INT NULL DEFAULT NULL',
    );

    protected $testKeys = array(
        'primary' => 'PRIMARY KEY (?cols)',
    );
}