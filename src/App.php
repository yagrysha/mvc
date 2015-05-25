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

	public function __construct($env = 'dev')
	{
		$this->env = 'dev';
		$this->conf = require_once APP_DIR . 'config_' . $this->env . '.php';
		$this->req = new Request();
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
		pe($this->conf['access']);
		if(!empty($this->conf['access'][$module][$controller])){

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

			$controller = new $class($this);
			$controller->run();
		}catch (Exception $e){
			$e->process($this);
		}
	}
}