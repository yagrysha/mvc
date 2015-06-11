<?php
return [
	function($uri){
		$uri = trim($uri, ' /');
		switch ($uri){
			case '':
				return ['action'=>'index'/*, 'cacheTime'=>3600*/];
			default:
				if(!strpos($uri,'/')){
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