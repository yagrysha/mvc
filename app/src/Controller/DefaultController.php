<?php
namespace myApp\Controller;

use Yagrysha\MVC\Controller;

class DefaultController extends Controller
{
	public function indexAction()
	{
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