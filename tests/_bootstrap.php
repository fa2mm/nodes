<?php

use \app\core\base\Base;

DEFINE('BASE_PATH', dirname(__DIR__));

require_once(__DIR__ . '/helpers/ConsoleRunner.php');
require_once(BASE_PATH . '/app/config/_test.php');
require_once(BASE_PATH . '/app/core/autoload.php');

$base = new Base($config);
