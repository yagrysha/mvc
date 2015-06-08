<?php
namespace myApp\Controller;
use Yagrysha\MVC\Controller;

class ErrorController extends Controller{

	protected function init(){
		if($this->app->env=='dev'){
			//p('dev ENV',$this->params);
		}
	}

	public function error404Action(){
		return ['_status'=>404];
	}

	public function error500Action(){
		return ['_status'=>500];
	}

	public function accessDeniedAction(){
		return ['text'=>''];
	}
}