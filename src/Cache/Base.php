<?php
namespace Yagrysha\MVC\Cache;

/**
 * no cache
 * @package Yagrysha\MVC\Cache
 */
class Base {

	private $options = [
	];

	public function __construct($options=[]){
		$this->options = array_merge($this->options, $options);
	}

	public function get($key, $lifetime=null){
		return null;
	}

	public function set($key, $data){
		return true;
	}

	public function setSetialize($key, $data){
		return true;
	}

	public function getSetialize($key, $lifetime=null){
		return null;
	}

	public function delete($key){
		return true;
	}
}