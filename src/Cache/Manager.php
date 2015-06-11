<?php
namespace Yagrysha\MVC\Cache;

class Manager {

	protected static $manager=[];

	public function get($type='File'){
		if(!isset(self::$manager[$type])){
			$type = 'Yagrysha\\MVC\\Cache\\'.$type;
			self::$manager[$type] = new $type();
		}
		return self::$manager[$type];
	}
}