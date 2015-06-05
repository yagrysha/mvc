<?php
namespace myApp\Controller\Test;

use Yagrysha\MVC\Controller;

class DefaultController extends Controller
{
	public function subAction()
	{
		return 'test SUB!';
	}
}