<?php

namespace Yagrysha\MVC;

use Yagrysha\MVC\Exception;

class TwigExtension extends \Twig_Extension
{
	/**
	 * @var Controller
	 */
	private $controller;

	public function getName()
	{
		return 'ymvc';
	}

	public function setController(Controller $controller)
	{
		$this->controller = $controller;
	}


	public function getFunctions()
	{
		return [
			'render' => new \Twig_Function_Method($this, 'render'),
		];
	}

	public function render($action, $params = [])
	{
		if (empty($action)) {
			return '';
		}
		$path = explode(':', $action);
		$params = [
			'data' => $params,
			'module' => $this->controller->params['module'],
			'controller' => $this->controller->params['controller'],
		];
		switch (count($path)) {
			case 1 :
				$params['action'] = $path[0];
				break;
			case 2 :
				$params['controller'] = $path[0];
				$params['action'] = $path[1];
				break;
			default:
				$params['module'] = $path[0];
				$params['controller'] = $path[1];
				$params['action'] = $path[2];
		}
		try {
			if ($params['controller'] == $this->controller->params['controller']
				&& $params['module'] == $this->controller->params['module']) {
				return $this->controller->run($params);
			}
			return $this->controller->app->runController($params);
		} catch (\Exception $e) {
			if ('dev' == $this->controller->app->env) {
				return $e;
			}
			return '';
		}
	}
}