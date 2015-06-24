<?php

namespace Yagrysha\MVC;

use Yagrysha\MVC\Cache\Manager as CacheManager;

abstract class Controller
{
	/**
	 * @var Request
	 */
	protected $req;
	protected $cacheConfig = [];
	/**
	 * @var User
	 */
	protected $user;
	/**
	 * @var App
	 */
	public $app;
	public $params;

	public function __construct(App $app)
	{
		$this->app = $app;
		$this->req = $app->req;
		$this->user = $app->user;
	}

	public function run($params)
	{
		$this->params = $params;
		$res = $this->init();
		if (null === $res) {
			$action = $params['action'] . 'Action';
			if (!method_exists($this, $action)) {
				throw new Exception(Exception::TYPE_500, ['message'=>'Action not found']);
			}
			$res = $this->$action();
		}
		$res = $this->postExecute($res);
		if (is_array($res)) {
			if (isset($res['_status'])) {
				$this->app->res->status($res['_status']);
				unset($res['_status']);
			}
			if (isset($res['_redirect'])) {
				$this->redirect($res['_redirect']);
				return '';
			}
			return $this->render($res);
		}
		return $res;
	}

	protected function getActionCache()
	{
		if (empty($this->cacheConfig[$this->params['action']])) {
			return null;
		}
		return CacheManager::get()->getSetialize($this->params+['cacheGroup'=>'controller'],
			$this->cacheConfig[$this->params['action']]);
	}

	protected function saveCache($key, $data)
	{
		return CacheManager::get()->setSetialize($key, $data);
	}

	protected function redirect($uri)
	{
		$this->app->res->location($_SERVER['REQUEST_SCHEME'] . '://' . HOST . '/' . ltrim($uri, '/'));
	}

	/**
	 * @return Response
	 */
	public function getResponse()
	{
		return $this->app->res;
	}

	protected function init()
	{
		$res = $this->getActionCache();
		if($res){
			return $res;
		}
		return null;
	}

	protected function postExecute($res)
	{
		if (!empty($this->cacheConfig[$this->params['action']])) {
			$this->saveCache($this->params+['cacheGroup'=>'controller'], $res);
		}
		return $res;
	}

	protected function render($data)
	{
		if (isset($data['_type'])) {
			$this->app->res->type($data['_type']);
			if (Response::TYPE_JSON == $data['_type']) {
				unset($data['_type']);
				return json_encode($data);
			}
		}
		if (isset($data['_content'])) {
			return $data['_content'];
		}
		return Render::get()->render($this, $data);
	}
}