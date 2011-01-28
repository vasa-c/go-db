<?php
/**
 * Центральный файл юнит-тестов, следует подключать из всех тестов
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB;

/* Путь к goDB. Изменить при переносе */
$PATH_TO_GODB = __DIR__.'/../goDB';

require_once(__DIR__.'/Base.php');
require_once(__DIR__.'/Config.php');

require_once($PATH_TO_GODB.'/autoload.php');
\go\DB\autoloadRegister();