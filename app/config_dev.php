<?php
const HOST = 't.loc';
const WEB_DIR='/';

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
	]
];