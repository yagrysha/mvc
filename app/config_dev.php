<?php
const HOST = 'mvc.loc';
const ROLE_GUEST = 0;
const ROLE_USER = 1;
const ROLE_ADMIN = 2;

const APP_NS = 'myApp';
const CACHE_ENABLED = true;

return [
	'routing'=> include_once APP_DIR . 'routing.php',
	'userClass' =>APP_NS.'\User',
	'cache'=>[
		'type'=>'File',
		'options'=>[
			'cache_dir' => APP_DIR.'cache/file/',
		]
	],
	//доступ уровня моудуля/ контроллера
	'access'=>[
		''=>[
			'default'=>[ROLE_GUEST, ROLE_USER]
		]
		/*'admin module'=>[
			'admin controller'=>[ROLE_ADMIN]
		]*/
	],
	'init'=>[
		function($app){
			//init Database
			\Yagrysha\ORM\Db::init([
				'host' => '127.0.0.1',
				'dbname'=>'test',
			]);
		}
	]
];