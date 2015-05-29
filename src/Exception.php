<?php
namespace Yagrysha\MVC;

class Exception extends \Exception{

	const TYPE_404 = 404;
	const TYPE_500 = 500;
	const TYPE_NOACCESS = 1;
	private $type;

	public function __construct($type){
		$this->type = $type;
		parent::__construct();
	}

	public function process(App $app){
		if($app->env=='dev'){
			print_r($this);
		}

		switch($this->type){
			case self::TYPE_NOACCESS:
				echo 'no permisions';
				break;
			case self::TYPE_404:
				echo 'page not found';
				break;
			default:
				echo 'server error';
		}
		return true;
	}
}