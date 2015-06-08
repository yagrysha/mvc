<?php
const HOST = 'mvc.loc';
const ROLE_GUEST = 0;
const ROLE_USER = 1;
const ROLE_ADMIN = 2;

const APP_NS = 'myApp';

return [
	'routing'=> include_once APP_DIR . 'routing.php',
	'userClass' =>APP_NS.'\User',
	//доступ уровня моудуля/ контроллера
	'access'=>[
		''=>[
			'default'=>[ROLE_GUEST]
		]
		/*'module'=>[
			'controller'=>[ROLE_GUEST]
		]*/
	],
	'init'=>[
		/*function($app){
			echo 'hello';
		}*/
	]
];