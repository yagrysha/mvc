<?php
$s = microtime(1);
require_once '../test/bootstrap.php';
$app = new \Yagrysha\MVC\App('dev');
$app->run();
pe('f', memory_get_peak_usage(), microtime(1)-$s);