<?php
namespace Yagrysha\MVC\Controller;
use Yagrysha\MVC\Controller;

class ErrorController extends Controller{

	public function init(){
		if($this->app->env=='dev'){
			print_r($this->params);
		}

	}

	public function error404Action(){
		$this->res->status(404);
		return '444';
	}

	public function error500Action(){
		$this->res->status(500);
		return '555';
	}

	public function noaccessAction(){
		return 'noa';
	}
}