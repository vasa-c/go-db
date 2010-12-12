<?php

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

header('Content-Type: text/html; charset=utf-8');

echo '<h1>go\DB 2</h1>';

require(__DIR__.'/goDB/autoload.php');
\go\DB\autoloadRegister();