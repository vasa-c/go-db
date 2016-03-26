<?php
/**
 * Connection parameters for travis-ci.org
 *
 * @package go\DB
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

return array(
    'mysql' => array(
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'dbname' => 'test',
    ),

    'mysqlold' => null,

    'sqlite' => array(
        'filename' => ':memory:',
    ),

    'pgsql' => [
        'host' => '127.0.0.1',
        'username' => 'postgres',
        'password' => '',
        'dbname' => 'travis_ci_test',
    ],
);
