<?php
namespace Yagrysha\MVC;

class Exception extends \Exception{

	const TYPE_404 = 1;
	const TYPE_500 = 2;
	const TYPE_NOACCESS = 3;
	private $type;

	public function __construct($type){
		$this->type = $type;
		parent::__construct();
	}

	public function getType(){
		return $this->type;
	}

	public function process(App $app){
		switch($this->type){
			case self::TYPE_NOACCESS:
				$action = 'noaccess';
				break;
			case self::TYPE_404:
				$action = 'error404';
				break;
			default:
				$action = 'error500';
		}
		try {
			$app->runController(
				[
					'controller' => 'error',
					'action' => $action,
					'data' => $this
				]
			);
		}catch (\Exception $e){
			die('error');
		}
	}
}