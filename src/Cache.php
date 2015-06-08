<?php
namespace Yagrysha\MVC;

//Todo ;
class Cache {

	const DEF_LIFETIME = 3600;
	private static $manager;

	public function getManager($type='File'){
		if(null==self::$manager){
			$type .= 'Cache';
			self::$manager = new $type();
		}
		return self::$manager;
	}

	public function get($key, $lifetime){
		return null;
	}

	public function set($key, $lifetime){
		return true;
	}

	public function clean(){
		return true;
	}
}