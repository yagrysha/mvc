<?php

namespace Yagrysha\MVC;

use Yagrysha\MVC\Cache\Manager as CacheManager;

abstract class Controller
{
    /**
     * @var Request
     */
    protected $req;
    protected $res;
    protected $cacheConfig = [
        //'action'=>time in sec,
    ];
    protected $access = [
        //'action'=>ROLES,
    ];

    /**
     * @var User
     */
    protected $user;
    /**
     * @var App
     */
    public $app;
    public $params;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->req = $app->req;
        $this->user = $app->user;
    }

    public function run($params)
    {
        $this->params = $params;
        $this->init();
        $this->checkAccess();
        $res = $this->getActionCache();
        if (empty($res)) {
            $action = $params['action'] . 'Action';
            if (!method_exists($this, $action)) {
                throw new Exception(Exception::TYPE_500, ['message' => 'Action not found']);
            }
            $res = $this->$action();
            $this->setActionCache($res);
        }
        if (is_array($res)) {
            return $this->render($res);
        }

        return $res;
    }

    private function checkAccess()
    {
        if (empty($this->access[$this->params['action']])
            || $this->user->hasRole($this->access[$this->params['action']])
        ) {
            return true;
        }
        throw new Exception(Exception::TYPE_ACCESSDENIED, $this->params);
    }

    protected function getActionCache()
    {
        if (CACHE_ENABLED && !empty($this->cacheConfig[$this->params['action']])) {
            return CacheManager::get()->getSetialize($this->params + ['cacheGroup' => 'controller'],
                $this->cacheConfig[$this->params['action']]);
        }

        return null;
    }

    protected function setActionCache($data)
    {
        if (CACHE_ENABLED && !empty($this->cacheConfig[$this->params['action']])) {
            CacheManager::get()->setSetialize($this->params + ['cacheGroup' => 'controller'], $data);
        }
    }

    protected function redirect($uri)
    {
        $this->app->res->location($_SERVER['REQUEST_SCHEME'] . '://' . HOST . '/' . ltrim($uri, '/'));

        return '';
    }

    protected function back($uri)
    {
        $this->app->res->back($_SERVER['REQUEST_SCHEME'] . '://' . HOST . '/' . ltrim($uri, '/'));

        return '';
    }

    protected function status($code)
    {
        $this->app->res->status($code);
    }

    protected function init()
    {
    }

    protected function render($data = [])
    {
        if (isset($data['_type'])) {
            $this->app->res->type($data['_type']);
            if (Response::TYPE_JSON == $data['_type']) {
                unset($data['_type']);

                return json_encode($data);
            }
        }
        if (isset($data['_content'])) {
            return $data['_content'];
        }

        return Render::get()->render($this, $data);
    }
}