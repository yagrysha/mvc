<?php
$s = microtime(1);
//require_once '../vendor/autoload.php';
require_once '../test/bootstrap.php';
$app = new \Yagrysha\MVC\App('dev');
$app->run();
h('end', memory_get_peak_usage(), microtime(1)-$s);