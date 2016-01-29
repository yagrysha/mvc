<?php
return [
    'env' => 'dev',
    'host' => 'mvc.loc',
    'url' => 'http://mvc.loc/',
    'root_dir' => realpath(__DIR__ . '/../'),
    'app_dir' => __DIR__,
    'app_ns' => 'myApp',
    'routing' => include __DIR__ . '/routing.php',
    'def_route' => [
        'module' => '',
        'controller' => 'default',
        'action' => 'index'
    ],
    'user' => [
        'class' => 'myApp\User'
    ],
    'cache' => [
        'enabled' => true,
        'type' => 'File',
        'options' => [
            'cache_dir' => __DIR__ . '/cache/file/',
        ]
    ],
    'render'=>[
        'type'=>'twig',
        'options'=>[
            'cache' => __DIR__ . '/cache/twig',
            'debug'=>true,
            'auto_reload'=>true,
            'strict_variables'=>true,
            //'autoescape'=>false,
            //'optimizations'=>0
        ],
        'templates'=>__DIR__.'/templates'
    ],
    //доступ уровня моудуля/ контроллера
    'access' => include __DIR__ . '/access.php',
    'init' => [
        function () {
            //init Database
            /*\Yagrysha\ORM\Db::init([
                'host' => '127.0.0.1',
                'dbname'=>'test',
            ]);*/
        }
    ]
];