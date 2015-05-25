<?php

namespace Yagrysha\MVC;

class Request
{
	private $_params = [];
	private $uri;
	public $ip;

	public function __construct($uri = '', $params = [])
	{
		if (empty($uri)) {
			$this->uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		}else{
			$this->uri = $uri;
		}
		$this->ip = (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] :
			(!empty($_SERVER["HTTP_X_REAL_IP"]) ? $_SERVER["HTTP_X_REAL_IP"] : (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '')));
		if (!empty($params)) {
			$this->setParams($params);
		}
	}

	function __get($key)
	{
		switch (true) {
			case isset($this->_params[$key]):
				return $this->_params[$key];
			case isset($_GET[$key]):
				return $_GET[$key];
			case isset($_POST[$key]):
				return $_POST[$key];
			/*case isset($_COOKIE[$key]):
				return $_COOKIE[$key];
			case ($key == 'REQUEST_URI'):
				return $this->getRequestUri();
			case isset($_SERVER[$key]):
				return $_SERVER[$key];
			case isset($_ENV[$key]):
				return $_ENV[$key];*/
			default:
				return null;
		}
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		if (null === $default) {
			return $this->__get($key);
		}
		$ret = $this->__get($key);
		return null === $ret ? $default : $ret;
	}

	/**
	 * Check to see if a property is set
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function __isset($key)
	{
		switch (true) {
			case isset($this->_params[$key]):
				return true;
			case isset($_GET[$key]):
				return true;
			case isset($_POST[$key]):
				return true;
			/*case isset($_COOKIE[$key]):
				return true;
			case isset($_SERVER[$key]):
				return true;
			case isset($_ENV[$key]):
				return true;*/
			default:
				return false;
		}
	}

	public function getRequestUri()
	{
		return $this->uri;
	}

	public function cookie($key)
	{
		return $_COOKIE[$key];
	}

	public function session($key)
	{
		return $_SESSION[$key];
	}


	public function setParam($key, $value)
	{
		$this->_params[$key] = $value;
	}

	public function setParams($params)
	{
		$this->_params = array_merge($this->_params, $params);
	}

	public function getParams()
	{
		return $this->_params;
	}

	public function isPost()
	{
		return (isset($_SERVER['REQUEST_METHOD']) && 'POST' == $_SERVER['REQUEST_METHOD']);
	}

	public function isXmlHttpRequest()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH']);
	}
}

