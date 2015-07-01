<?php
/**
 * @author Yaroslav Gryshanovich <yagrysha@gmail.com>
 */

namespace Yagrysha\MVC;

class Request
{
	private $_params = [];
	private $uri;
	public $ip;

	public function __construct($uri = '', $params = [])
	{
		if (empty($uri)) {
			$this->uri = $this->server('REQUEST_URI', '');
		} else {
			$this->uri = $uri;
		}
		$this->ip = empty($_SERVER["HTTP_X_FORWARDED_FOR"]) ?
			(empty($_SERVER["HTTP_X_REAL_IP"]) ? $this->server('REMOTE_ADDR', ''): $_SERVER["HTTP_X_REAL_IP"])
			:$_SERVER["HTTP_X_FORWARDED_FOR"];

		if (!empty($params)) {
			$this->setParams($params);
		}
		if(session_status() !== PHP_SESSION_ACTIVE){
			session_start();
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
			default:
				return null;
		}
	}

	/**
	 * @param string $key
	 * @param string $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
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
			default:
				return false;
		}
	}

	public function getRequestUri()
	{
		return $this->uri;
	}

	public function cookie($key, $def = null)
	{
		return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $def;
	}

	public function session($key, $def = null)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : $def;
	}

	public function server($key, $def = null)
	{
		return isset($_SERVER[$key]) ? $_SERVER[$key] : $def;
	}

	public function env($key, $def = null)
	{
		return isset($_ENV[$key]) ? $_ENV[$key] : $def;
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
		return 'POST' == $this->server('REQUEST_METHOD');
	}

	public function isXmlHttpRequest()
	{
		return 'XMLHttpRequest' == $this->server('HTTP_X_REQUESTED_WITH');
	}
}

