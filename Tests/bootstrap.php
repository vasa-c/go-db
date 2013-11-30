<?php
/**
 * Центральный файл юнит-тестов, следует подключать из всех тестов
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB;

require(__DIR__.'/Base.php');
require(__DIR__.'/Config.php');
require(__DIR__.'/../goDB/Autoloader.php');

\go\DB\Autoloader::register();
\go\DB\Autoloader::registerForTests(__NAMESPACE__, __DIR__.'/DB');
