<?php
return [
	function(\Yagrysha\MVC\Request $req){
		switch (parse_url($req->getRequestUri(), PHP_URL_PATH)){
			case '/' :
				return ['action'=>'index'];
			case '/contact':
				return ['action'=>'contact'];
		}
		return false;
	},
	'/([a-z]+)/?([a-z]*)/?([a-z0-9-_]*)'=>array(
		1=>'controller', // позиция подмаски - значение парметра
		2=>'action',
		3=>'data',
		'action'=>'index' //default
	),
];