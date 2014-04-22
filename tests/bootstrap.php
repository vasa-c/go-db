<?php
/**
 * @package go\DB
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace go\Tests\DB;

require(__DIR__.'/../goDB/Autoloader.php');

\go\DB\Autoloader::register();
\go\DB\Autoloader::registerForTests(__NAMESPACE__, __DIR__);
