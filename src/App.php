<?php
/**
 * @author Yaroslav Gryshanovich <yagrysha@gmail.com>
 */

namespace Yagrysha\MVC;
use Yagrysha\MVC\Cache\Manager as Cache;

if (!defined('ROOT_DIR')) {
	define('ROOT_DIR', realpath(__DIR__ . '/../'));
}
define('APP_DIR', ROOT_DIR . '/app/');

class App
{
	public $env = 'dev';
	public $conf;
	/**
	 * @var Request
	 */
	public $req;
	/**
	 * @var Response
	 */
	public $res;
	/**
	 * @var User
	 */
	public $user;

	private $defRouteParams = [
		'module' => '',
		'controller' => 'default',
		'action' => 'index'
	];

/*    private static $app;

    public static function get($env = 'dev'){
        if(null===self::$app){
            self::$app = new self($env);
        }
        return self::$app;
    }*/

	public function __construct($env = 'dev')
	{
		$this->env = $env;
		$this->conf = require APP_DIR . 'config_' . $this->env . '.php';
		$this->req = new Request();
		$userClass = $this->conf['userClass']?:'Yagrysha\\MVC\\User';
		$this->user = $userClass::getUser($this->req);
	}

	public function setRequest(Request $request)
	{
		$this->req = $request;
	}

	public function checkRoute($uri)
	{
		foreach ($this->conf['routing'] as $pattern => $data) {
			if (is_callable($data)) {
				if($params = $data($uri)) {
					$params = array_merge($this->defRouteParams, $params);
					break;
				}
			} else {
				$matches = [];
				if (preg_match('`^' . $pattern . '`', $uri, $matches)) {
					$params = array_merge($this->defRouteParams, $data);
					foreach ($matches as $k => $match) {
						if (is_int($k)) {
							if (!empty($data[$k]) && !empty($match)) {
								$params[$data[$k]] = $match;
							}
							unset($params[$k]);
						}
					}
					break;
				}
			}
		}
		return $params;
	}

	private function checkAccess($module, $controller)
	{
		if (empty($this->conf['access'][$module][$controller])
			|| $this->user->hasRole($this->conf['access'][$module][$controller])
		) {
			return true;
		}
		throw new Exception(Exception::TYPE_ACCESSDENIED);
	}

	private function init()
	{
		if (!empty($this->conf['init'])) {
			foreach ($this->conf['init'] as $mw) {
				if (is_callable($mw)) {
					$mw($this);
				}
			}
		}
		Cache::init($this->conf['cache']);
	}

	public function run()
	{
		$this->res = new Response();
		try {
			$this->init();
			$params = $this->checkRoute($this->req->getRequestUri());
			$this->checkAccess($params['module'], $params['controller']);
			$this->req->setParams($params);
			if(empty($params['cacheTime']) || !CACHE_ENABLED){
				$content = $this->runController($params);
			}else{
				$this->res->setCacheHeader($params['cacheTime']);
				$content = $this->runController($params, $params['cacheTime']);
			}
		} catch (Exception $e) {
			$content = $e->process($this);
		}
		$this->res->setContent($content);
		$this->res->sendContent();
	}

	public function runController($params, $cacheTime=null)
	{
		$params = array_merge($this->defRouteParams, $params);
		if(CACHE_ENABLED && $cacheTime){
			$cachekey='app/'.md5(serialize($params)).$cacheTime;
			$cache = Cache::get();
			$ret = $cache->get($cachekey, $cacheTime);
			if($ret) return $ret;
		}
		$module = $params['module'] ? ('\\' . ucfirst($params['module'])):'';
		$controller = ucfirst($params['controller']);
		$class = APP_NS."\\Controller$module\\{$controller}Controller";
		if (!class_exists($class)) {
			throw new Exception(Exception::TYPE_404);
		}
		$controller = new $class($this);
		if(CACHE_ENABLED && $cacheTime){
			$ret = $controller->run($params);
			$cache->set($cachekey, $ret);
			return $ret;
		}
		return $controller->run($params);
	}
}