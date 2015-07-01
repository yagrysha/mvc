<?php
/**
 * @author Yaroslav Gryshanovich <yagrysha@gmail.com>
 */

namespace Yagrysha\MVC;

class Exception extends \Exception{

	const TYPE_404 = 1;
	const TYPE_500 = 2;
	const TYPE_ACCESSDENIED = 3;
	const TYPE_ = 4;
	private $type;
	private $data=[];

	public function __construct($type, $data =null){
		$this->type = $type;
		$this->data = $data;
		parent::__construct();
	}

	public function getType(){
		return $this->type;
	}

	public function getData(){
		return $this->data;
	}

	public function process(App $app){
		switch($this->type){
			case self::TYPE_ACCESSDENIED:
				$action = 'accessDenied';
				break;
			case self::TYPE_404:
				$action = 'error404';
				break;
			default:
				$action = 'error500';
		}
		try {
			return $app->runController(
				[
					'controller' => 'error',
					'action' => $action,
					'data' => $this
				]
			);
		}catch (\Exception $e){
			die('error Exception'.$e);
		}
	}
}