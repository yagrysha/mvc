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

	private $defRouteParams = [
		'module' => '',
		'controller' => 'default',
		'action' => 'index'
	];

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

	public function checkRoute($uri)
	{
		foreach ($this->conf['routing'] as $pattern => $data) {
			if (is_callable($data) && $params = $data($this->req)) {
				$params = array_merge($this->defRouteParams, $params);
				break;
			} else {
				$matches = [];
				if (preg_match('`^' . $pattern . '`', $uri, $matches)) {
					$params = array_merge($this->defRouteParams, $data);
					foreach ($matches as $k => $match) {
						if (is_int($k)) {
							if (!empty($data[$k]) && !empty($match)) {
								$params[$data[$k]] = $match;
							}
							unset($params[$k]);
						}
					}
					break;
				}
			}
		}
		return $params;
	}

	private function checkAccess($module, $controller)
	{
		if (!empty($this->conf['access'][$module][$controller])
			&& !$this->user->hasRole($this->conf['access'][$module][$controller])
		) {
			throw new Exception(Exception::TYPE_NOACCESS);
		}
		return true;
	}

	private function init()
	{
		if (!empty($this->conf['init'])) {
			foreach ($this->conf['init'] as $mw) {
				if (is_callable($mw)) {
					$mw($this);
				}
			}
		}
	}

	public function run()
	{
		try {
			$this->init();
			$params = $this->checkRoute($this->req->getRequestUri());
			$this->checkAccess($params['module'], $params['controller']);
			$this->req->setParams($params);
			$this->runController($params, true);
		} catch (Exception $e) {
			$e->process($this);
		}
	}

	public function runController($params, $throw404 = false)
	{
		$module = empty($params['module']) ? '' : ('\\' . ucfirst($params['module']));
		$controller = ucfirst($params['controller']);
		$class = APP_NS."\\Controller$module\\{$controller}Controller";
		if (!class_exists($class)) {
			if ($throw404) {
				throw new Exception(Exception::TYPE_404);
			} else {
				return '';
			}
		}
		$controller = new $class($this);
		$controller->run($params);
	}
}