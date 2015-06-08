<?php
namespace myApp\Controller;

use Yagrysha\MVC\Controller;

class DefaultController extends Controller
{
	public function init(){
		//pe($this->user);
	}

	public function indexAction()
	{
		return [
			'text'=>'hello/ index page'
		];
	}

	public function privateAction(){
		if(!$this->user->isLogged()){
			return ['_redirect'=>'login'];
		}
	}

	public function contactAction()
	{
		return [
			'text' => 'hello wold, app controller'
		];
	}

	public function subAction()
	{
		return [
			'text' => ' DEF SUB '
		];
	}

	public function login(){
		return [
			'text'=>'login page'
		];
	}
}