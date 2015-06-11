<?php
namespace myApp\Controller;

use Yagrysha\MVC\Controller;
use Yagrysha\MVC\Cache\Manager as Cache;

class DefaultController extends Controller
{
	protected $cacheConfig = [
		'cached'=>100,
	];

	public function init(){
		//pe($this->user);
	//	$ret = $this->cache();
	}


	public function indexAction()
	{
		/*$c= Cache::get();
		p($c);*/
		return [
			'text'=>'hello/ index page'
		];
	}

	public function privateAction(){
		if(!$this->user->isLogged()){
			return ['_redirect'=>'login'];
		}
	}

	public function cachedAction(){
		return time();
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