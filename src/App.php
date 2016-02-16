<?php
/**
 * @author Yaroslav Gryshanovich <yagrysha@gmail.com>
 */

namespace Yagrysha\MVC;

use Yagrysha\MVC\Cache\Manager as Cache;

class App
{
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

    private static $app;

    public static function ini($appPath, $env)
    {
        self::$app = new self(require $appPath . '/config_' . $env . '.php');
        return self::$app;
    }

    public static function get()
    {
        return self::$app;
    }

    public static function config($name)
    {
        $parts = explode('.', $name);
        $ret = self::$app->conf;
        foreach ($parts as $part) {
            if (isset($ret[$part])) {
                $ret = $ret[$part];
            } else {
                return null;
            }
        }

        return $ret;
    }

    private function __construct($config)
    {
        $this->conf = $config;
        $this->init();
        $this->req = new Request();
        if(!empty($this->conf['user']['class'])) {
            $userClass = $this->conf['user']['class'];
            $this->user = $userClass::getUser($this->req);
        }
    }

    public function setRequest(Request $request)
    {
        $this->req = $request;
    }

    public function checkRoute($uri)
    {
        $params = [];
        foreach ($this->conf['routing'] as $pattern => $data) {
            if (is_callable($data)) {
                if ($params = $data($uri)) {
                    $params = array_merge($this->conf['def_route'], $params);
                    break;
                }
            } else {
                $matches = [];
                if (preg_match('`^' . $pattern . '`', $uri, $matches)) {
                    $params = array_merge($this->conf['def_route'], $data);
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
                    $mw();
                }
            }
        }
        if (!empty($this->conf['cache'])) {
            Cache::init($this->conf['cache']);
        }
    }

    public function run()
    {
        $this->res = new Response();
        try {
            $params = $this->checkRoute($this->req->getRequestUri());
            $this->checkAccess($params['module'], $params['controller']);
            $this->req->setParams($params);
            if (empty($params['cacheTime']) || !$this->conf['cache']['enabled']) {
                $content = $this->runController($params);
            } else {
                $this->res->setCacheHeader($params['cacheTime']);
                $content = $this->runController($params, $params['cacheTime']);
            }
        } catch (Exception $e) {
            $content = $e->process($this);
        }
        $this->res->setContent($content);
        $this->res->sendContent();
    }

    public function runController($params, $cacheTime = null)
    {
        $params = array_merge($this->conf['def_route'], $params);
        $cacheEnabled = ($cacheTime && $this->conf['cache']['enabled']);
        if ($cacheEnabled) {
            $cacheKey = 'app/' . md5(serialize($params)) . $cacheTime;
            $cache = Cache::get();
            $ret = $cache->get($cacheKey, $cacheTime);
            if ($ret) {
                return $ret;
            }
        }
        $module = $params['module'] ? ('\\' . ucfirst($params['module'])) : '';
        $controller = ucfirst($params['controller']);
        $class = $this->conf['app_ns'] . "\\Controller$module\\{$controller}Controller";
        if (!class_exists($class)) {
            throw new Exception(Exception::TYPE_404);
        }
        $controller = new $class($this);
        if ($cacheEnabled) {
            $ret = $controller->run($params);
            $cache->set($cacheKey, $ret);
            return $ret;
        }

        return $controller->run($params);
    }
}