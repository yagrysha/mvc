<?php
namespace Yagrysha\MVC\Cache;

//TODO сделать его
class Redis {

	const DEF_LIFETIME = 3600;

	public function get($key, $lifetime){
		return null;
	}

	public function set($key){
		return true;
	}

	public function clean(){
		return true;
	}
}