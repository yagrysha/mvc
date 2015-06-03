<?php
namespace myApp\Controller;

use Yagrysha\MVC\Controller;
use Yagrysha\MVC\Render;

class DefaultController extends Controller
{
	public function indexAction()
	{

		$s = microtime(1);
		for($i=0;$i<100;$i++) {
			Render::renderPhp(
				'default/index.php',
				[
					'text' => 'hh'
				]
			);
		}
		echo microtime(1)-$s." p\n\n";

		$s = microtime(1);
		for($i=0;$i<100;$i++) {
			Render::renderTwig(
				'default/index.html.twig',
				[
					'text' => 'hh'
				]
			);
		}
		echo microtime(1)-$s." twig\n\n";
		pe(1);
		return [
			'_view'=>'php',
			'_type'=>'html',
			'text'=>'app controller'
		];
	}

	public function contactAction()
	{
		return [
			'text' => 'hello wold, app controller'
		];
	}
}