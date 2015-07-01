#!/usr/bin/env php
<?php
//require_once '../vendor/autoload.php';
require_once '../test/bootstrap.php';
use Yagrysha\MVC\App;
$app = new App('dev');
$app->runController([
    'action'=>'cli',
    'argv'=>$argv
]);