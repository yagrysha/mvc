<?php

namespace Yagrysha\MVC;

abstract class Controller
{
	/**
	 * @var Request
	 */
	protected $req;
	/**
	 * @var Response
	 */
	protected $res;
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
		$this->res = $app->res;
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
		if (is_array($res)) {
			if (isset($res['_status'])) {
				$this->res->status($res['_status']);
				unset($res['_status']);
			}
			if (isset($res['_redirect'])) {
				$this->res->location($_SERVER['REQUEST_SCHEME'] . '://' . HOST . '/' . $res['_redirect']);
				return '';
			}
			return $this->render($res);
		}
		return $res;
	}

	/**
	 * @return Response
	 */
	public function getResponse(){
		return $this->res;
	}

	protected function init()
	{
	}

	protected function render($data)
	{
		if (isset($data['_type'])) {
			$this->res->type($data['_type']);
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