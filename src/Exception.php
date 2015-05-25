<?php
namespace Yagrysha\MVC;

class Exception extends \Exception{

	const TYPE_404 = 404;
	const TYPE_500 = 500;

	public function __construct($type){
		parent::__construct();
	}

	public function process(App $app){
		if($app->env=='dev'){
			print_r($this);
		}
		//todo
		echo 'error';
		return true;
	}
}