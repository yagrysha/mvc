<?php
/**
 * @author Yaroslav Gryshanovich <yagrysha@gmail.com>
 */

namespace Yagrysha\MVC;

class Render
{
	private static $instance;
	private $twig;

	private function __construct()
	{
		$options = [
			'cache' => APP_DIR . 'cache/twig',
			//'debug'=>true,
			'auto_reload'=>true,
			'strict_variables'=>true,
			//'autoescape'=>false,
			//'optimizations'=>0
		];
		\Twig_Autoloader::register();
		$this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem(APP_DIR . 'templates'), $options);
		$this->twig->addExtension(new TwigExtension());
	}

	static function get()
	{
		if (null == self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function render(Controller $controller, array $data = [])
	{
		if (empty($data['_tpl'])) {
			$tpl = ($controller->params['module'] ? $controller->params['module'] . DIRECTORY_SEPARATOR : '')
				. $controller->params['controller'] . DIRECTORY_SEPARATOR . $controller->params['action'];
		} else {
			$tpl = $data['_tpl'];
		}
		$tpl .= '.' . (empty($data['_type']) ? Response::TYPE_HTML : $data['_type']);
		unset($data['_tpl'], $data['_type']);
		$this->twig->getExtension('ymvc')->setController($controller);
		return $this->twig->render($tpl, $data);
	}
}