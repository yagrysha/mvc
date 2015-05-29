<?php

namespace Yagrysha\MVC;

abstract class Controller
{
	const DEF_ACTION = 'index';
	protected $req;
	protected $res;
	protected $app;
	protected $params;

	public function __construct(App $app)
	{
		$this->app = $app;
		$this->req = $app->req;
		$this->user = $app->user;
		$this->res = new Response();
	}

	public function run($params)
	{
		if (empty($params['action'])) {
			$params['action'] = self::DEF_ACTION;
		}
		$action = $params['action'].'Action';
		if(!method_exists($this, $action)){
			throw new Exception(Exception::TYPE_500);
		}
		$this->init();
		$res = $this->$action();
		$this->res->setData($res);
		$this->res->render();
	}

	protected function init()
	{
	}

	protected function redirect($uri){
		$this->res->location($_SERVER['REQUEST_SCHEME'].'://'.HOST.'/'.$uri);
	}
}