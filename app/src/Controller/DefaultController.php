<?php
namespace myApp\Controller;

use Yagrysha\MVC\Controller;

class DefaultController extends Controller
{
	public function indexAction()
	{
		return [
			'text'=>'hello/ index page'
		];
	}

	public function contactAction()
	{
		return [
			'text' => 'hello wold, app controller'
		];
	}

	public function subAction()
	{
		//pe($this);
		return [
			'text' => ' DEF SUB '
		];
	}
}