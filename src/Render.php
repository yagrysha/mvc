<?php

namespace Yagrysha\MVC;
class Render
{
	private static $twig;
	private static $smarty;
	const DEF_RENDERER = 'Twig';

	static function getTwig(){
		if(null==self::$twig){

		}
		return self::$twig;
	}

	static function getSmarty(){
		if(null==self::$smarty){
			$smarty = new \Smarty();
			$smarty->setTemplateDir(APP_DIR.'templates');
			$smarty->setCompileDir(APP_DIR . 'cache/templates_c');
			//$smarty->setPluginsDir(APP_DIR.'plugins');
			$smarty->error_reporting = E_ALL ^ E_NOTICE;
			//$smarty->compile_check = false;//if prod
			self::$smarty = $smarty;
		}
		return self::$smarty;
	}

	static function renderTwig($data){
		$twig = self::getTwig();
		$twig->assign($data);
		return $output = $twig->fetch($data['_tpl'].(empty($data['_type'])?'':('.'.$data['_type'])).'.twig');
	}

	static function renderSmarty($data){
		$smarty = self::getSmarty();
		$smarty->assign($data);
		return $output = $smarty->fetch($data['_tpl'].(empty($data['_type'])||$data['_type']=='html'?'':('.'.$data['_type'])).'.tpl');
	}

	static function renderPhp($data){
		ob_start();
		extract($data);
		include APP_DIR.'templates'.DIRECTORY_SEPARATOR.$data['_tpl'].'.phtml';
		return ob_get_clean();
	}

	static public function run(array $data){
		$render = 'render'.ucfirst(empty($data['_view'])?self::DEF_RENDERER:$data['_view']);
		unset($data['_view']);
		return Render::$render($data);
	}
}