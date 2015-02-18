<?php
/**
 * @package go\DB
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace go\Tests\DB;

use go\DB\Autoloader;

require(__DIR__.'/../goDB/Autoloader.php');

Autoloader::register();
Autoloader::registerForTests(__NAMESPACE__, __DIR__);
