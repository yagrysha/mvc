#!/usr/bin/env php
<?php
$s = microtime(1);
//require_once '../vendor/autoload.php';
require_once '../test/bootstrap.php';
use Yagrysha\MVC\App;
App::ini(realpath(__DIR__.'/../app'),'dev')->runController([
    'action'=>'cli',
    'argv'=>$argv
]);
p('end', memory_get_peak_usage(), microtime(1)-$s);