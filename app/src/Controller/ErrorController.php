<?php
namespace myApp\Controller;
use Yagrysha\MVC\App;

class ErrorController extends \Yagrysha\MVC\Controller {

	protected function init(){
		if('dev'==App::config('env')){
			//p('dev ENV',$this->params);
		}
	}

	public function error404Action(){
        $this->status(404);
		return [];
	}

	public function error500Action(){
		$exception = $this->params['data'];
        $this->status(500);
		return $exception->getData();
	}

	public function accessDeniedAction(){
		if($this->user->isLogged()){
			echo 'logged';
		}else{
			echo 'not logged!';
		}
		return ['text'=>'vh'];
	}
}