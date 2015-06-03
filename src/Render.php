<?php

namespace Yagrysha\MVC;
class Render
{
	private static $twig;

	static function getTwig(){
		if(null==self::$twig){
			\Twig_Autoloader::register();
			self::$twig = new \Twig_Environment(new \Twig_Loader_Filesystem(APP_DIR.'templates'), [
				'cache' => APP_DIR . 'cache/twig',
				//'debug'=>true,
				//'auto_reload'=>true,
				//'strict_variables'=>true,
				//'autoescape'=>false,
				//'optimizations'=>0
			]);
			//$twig->addExtension(new App_Twig_Extension());
		}
		return self::$twig;
	}

	static function renderPhp($tpl, $data=[]){
		ob_start();
		extract($data);
		include APP_DIR.'templates'.DIRECTORY_SEPARATOR.$tpl.'.php';
		return ob_get_clean();
	}

	static public function render($tpl, array $data=[]){
		if(isset($data['_view']) && 'php'==$data['_view']){
			unset($data['_view']);
			return self::renderPhp($tpl, $data);
		}
		return	self::getTwig()->render($tpl, $data);

	}
}