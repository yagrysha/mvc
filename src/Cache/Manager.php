<?php
namespace Yagrysha\MVC\Cache;

class Manager {

	/**
	 * @var Base[]
	 */
	protected static $cache=[];

	public function init($config){
		if(!empty($config['class'])){
			$class = $config['class'];
		}else{
			$class = 'Yagrysha\\MVC\\Cache\\'.$config['type'];
		}
		self::$cache[$config['type']] = new $class($config['options']);
	}
	/**
	 * @param string $type
	 * @return Base
	 */
	public static function get($type=''){
		if(empty($type)) $type = key(self::$cache);
		return self::$cache[$type];
	}
}