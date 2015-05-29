<?php

namespace Yagrysha\MVC;

if (!defined('ROOT_DIR')) {
	define('ROOT_DIR', realpath(__DIR__ . '/../'));
}
define('APP_DIR', ROOT_DIR . '/app/');

class App
{
	public $env = 'dev';
	private $conf;
	public $req;
	public $user;

	public function __construct($env = 'dev')
	{
		$this->env = $env;
		$this->conf = require_once APP_DIR . 'config_' . $this->env . '.php';
		$this->req = new Request();
		$this->user = User::getUser($this->req);
	}

	public function setRequest(Request $request)
	{
		$this->req = $request;
	}

	public function checkRoute($uri){
		$params=[
			'module'=>'',
			'controller'=>'default',
		];
		$uri= trim($uri, '/ ');
		if('/'!=WEB_DIR){
			//TODO отбросить папку
		}
		foreach ($this->conf['routing'] as $pattern=>$data) {
			$matches=[];
			if(preg_match('`^'.$pattern.'`', $uri, $matches)){
				$params = array_merge($params, $data);
				foreach($matches as $k=>$match){
					if(is_int($k)) {
						if (!empty($data[$k]) && !empty($match)) {
							$params[$data[$k]] = $match;
						}
						unset($params[$k]);
					}
				}
				break;
			}
		}
		return $params;
	}

	private function checkAccess($module, $controller){
		if(!empty($this->conf['access'][$module][$controller])
			&& !$this->user->hasRole($this->conf['access'][$module][$controller])){
			throw new Exception(Exception::TYPE_NOACCESS);
		}
		return true;
	}

	public function run()
	{
		try {
			$params = $this->checkRoute($this->req->getRequestUri());
			$this->checkAccess($params['module'], $params['controller']);
			$this->req->setParams($params);
			$module = empty($params['module'])?'':('\\'.ucfirst($params['module']));
			$controller = ucfirst($params['controller']);
			$class = "Yagrysha\\MVC\\Controller$module\\{$controller}Controller";
//todo
			$controller = new $class($this);
			$controller->run($params);
		}catch (Exception $e){
			$e->process($this);
		}
	}

	public function controller($params){
		$module = empty($params['module'])?'':('\\'.ucfirst($params['module']));
		$controller = ucfirst($params['controller']);
		$class = "Yagrysha\\MVC\\Controller$module\\{$controller}Controller";
		$controller = new $class($this);
		$controller->run($params);
		//todo;
	}
}