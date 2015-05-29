<?php
const HOST = 'mvc.loc';
const ROLE_GUEST = 0;
const ROLE_USER = 1;
const ROLE_ADMIN = 2;

return [
	'routing'=>[
		// toto сделать дефолтный роутинг без регулярки
		'([a-z]+)/?([a-z]*)/?([a-z0-9-_]*)'=>array(
			1=>'controller', // позиция подмаски - значение парметра
			2=>'action',
			3=>'data',
			'action'=>'index' //default
		)
	],
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
		function($app){
			echo 'hello';
			p($app);
		}
	]
];