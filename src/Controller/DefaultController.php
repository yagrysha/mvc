<?php
namespace Yagrysha\MVC\Controller;
use Yagrysha\MVC\Controller;
use Yagrysha\MVC\Exception;

class DefaultController extends Controller{

	public function init(){
		//todo
		//cache control
		//access con
		//parent::run();
	}

	public function indexAction(){
	//	pe($_SERVER);
		//throw new Exception(Exception::TYPE_404);
		//throw new Exception(Exception::TYPE_500);
//		throw new Exception(Exception::TYPE_NOACCESS);
		//$this->res->type(Res);
		return 'content';
	}

	public function testactAction(){

	}
}