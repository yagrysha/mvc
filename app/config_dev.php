<?php
const ROLE_GUEST = 0;
const ROLE_USER = 1;
const ROLE_ADMIN = 2;

const HOST = 't.loc';
//const ROOT_DIR='/';
//define('APP_DIR', ROOT_DIR . '/app/');
const WEB_DIR='/';
const WEB_ROOT='/web/';

return [
	'routing'=>[
		'([a-z]+)/?([a-z]*)/?([a-z0-9-_]*)'=>array(
			1=>'controller',
			2=>'action',
			3=>'data',
			'action'=>'index' //default
		)
	],
	'access'=>[
		''=>[
			'default'=>[ROLE_GUEST]
		]
		/*'module'=>[
			'controller'=>[ROLE_GUEST]
		]*/
	]
];