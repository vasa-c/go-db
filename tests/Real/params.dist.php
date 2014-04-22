<?php
/**
 * Connection parameters for testing real databases
 *
 * A key is a adapter name, a value is connection parameters.
 *
 * Copy this file to params.php
 *
 * @package go\DB
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

return array(
    'mysql' => array(
        'host' => 'localhost',
        'username' => 'test',
        'password' => 'test',
        'dbname' => 'test',
    ),

    'mysqlold' => array(
        'host' => 'localhost',
        'username' => 'test',
        'password' => 'test',
        'dbname' => 'test',
    ),

    'sqlite' => array(
        'filename' => ':memory:',
    ),

    'pgsql' => null,
);
