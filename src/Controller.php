<?php

namespace Yagrysha\MVC;

abstract class Controller
{
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
		$action = $params['action'] . 'Action';
		if (!method_exists($this, $action)) {
			throw new Exception(Exception::TYPE_500);
		}
		$this->params = $params;
		$this->init();
		$res = $this->$action();
		if(is_array($res)){
			$this->render($res);
		}else{
			$this->res->setContent($res);
		}
		$this->res->sendContent();
	}

	protected function init()
	{
	}

	protected function redirect($uri)
	{
		$this->res->location($_SERVER['REQUEST_SCHEME'] . '://' . HOST . '/' . $uri);
	}

	protected function render($data)
	{
		if(isset($data['_status'])){
			$this->res->status($data['_status']);
		}
		if(isset($data['_type'])){
			$this->res->type($data['_type']);
			if(Response::TYPE_JSON==$data['_type']){
				unset($data['_type']);
				$this->res->setContent($data);
				return;
			}
		}
		if(isset($data['_content'])){
			$this->res->setContent($data['_content']);
			return;
		}
		if(empty($data['_tpl'])){
			$data['_tpl'] = ($this->params['module']?$this->params['module'].DIRECTORY_SEPARATOR:'')
				.$this->params['controller'].DIRECTORY_SEPARATOR.$this->params['action'];
		}elseif(strpos($data['_tpl'], '/')===false){
			$data['_tpl'] = ($this->params['module']?$this->params['module'].DIRECTORY_SEPARATOR:'')
				.$this->params['controller'].DIRECTORY_SEPARATOR.$data['_tpl'];
		}
		$this->res->setContent(Render::run($data));
	}
}