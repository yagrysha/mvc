<?php
return [
	function(\Yagrysha\MVC\Request $req){
		$uri = parse_url($req->getRequestUri(), PHP_URL_PATH);
		switch ($uri){
			case '/' :
				return ['action'=>'index'];
			default:
				if(!strpos($uri,'/',1)){
					return ['action'=>$uri];
				}
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