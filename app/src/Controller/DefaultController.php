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
		return [
			'text'=>'hello/ index page'
		];
	}

	public function privateAction(){
		if(!$this->user->isLogged()){
			return ['_redirect'=>'login'];
		}
		p($this->user->getData());
		return 'ello';
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

	public function loginAction(){
		if($this->req->isPost()){
			$login = $this->req->get('login');
			$this->user->login(['id'=>1,'login'=>$login, 'tt'=>time(), 'roles' => [ROLE_GUEST, 'testouser'], 'name'=>'dfg'], true);
			return ['_redirect'=>'private'];
		}

		return [
			'text'=>'login page'
		];
	}

}