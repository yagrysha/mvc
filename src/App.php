<?php

namespace Yagrysha\MVC;
use Yagrysha\MVC\Exception;

if (!defined('ROOT_DIR')) {
	define('ROOT_DIR', realpath(__DIR__ . '/../'));
}
define('APP_DIR', ROOT_DIR . '/app/');

class App
{
	public $env = 'dev';
	private $conf;
	/**
	 * @var Request
	 */
	public $req;
	/**
	 * @var Response
	 */
	public $res;
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
		$userClass = $this->conf['userClass']?:'User';
		$this->user = $userClass::getUser($this->req);
	}

	public function setRequest(Request $request)
	{
		$this->req = $request;
	}

	public function checkRoute($uri)
	{
		foreach ($this->conf['routing'] as $pattern => $data) {
			if (is_callable($data)) {
				if($params = $data($this->req)) {
					$params = array_merge($this->defRouteParams, $params);
					break;
				}
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
			throw new Exception(Exception::TYPE_ACCESSDENIED);
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
		$this->res = new Response();
		try {
			$this->init();
			$params = $this->checkRoute($this->req->getRequestUri());
			$this->checkAccess($params['module'], $params['controller']);
			$this->req->setParams($params);
			$content = $this->runController($params);
		} catch (Exception $e) {
			$content = $e->process($this);
		}
		$this->res->setContent($content);
		$this->res->sendContent();
	}

	public function runController($params, $cacheTime=null)
	{
		$params = array_merge($this->defRouteParams, $params);
		if($cacheTime){
			$cachekey=md5(serialize($params)).$cacheTime;
			$cache = Cache::getManager($this->conf['cache']?:'');
			$ret = $cache->get($cachekey);
			if($ret) return $ret;
		}
		$module = $params['module'] ? ('\\' . ucfirst($params['module'])):'';
		$controller = ucfirst($params['controller']);
		$class = APP_NS."\\Controller$module\\{$controller}Controller";
		if (!class_exists($class)) {
			throw new Exception(Exception::TYPE_404);
		}
		$controller = new $class($this);
		if($cacheTime){
			$ret = $controller->run($params);
			$cache->set($ret);
			return $ret;
		}
		return $controller->run($params);
	}
}