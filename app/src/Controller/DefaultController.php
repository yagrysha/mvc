<?php
namespace myApp\Controller;

class DefaultController extends \Yagrysha\MVC\Controller
{
    protected $cacheConfig = [
        'cached' => 100,
    ];

    protected $access = [
        'private' => 'user',
    ];

    public function indexAction()
    {
        return [
            'text' => 'hello/ index page'
        ];
    }

    public function privateAction()
    {
        p($this->user->getData());

        return 'ello';
    }

    public function cachedAction()
    {
        return time();
    }

    public function contactAction()
    {
        return [
            'text' => 'hello wold, app controller'
        ];
    }

    public function subAction()
    {
        return [
            'text' => ' DEF SUB '
        ];
    }

    public function loginAction()
    {
        if ($this->req->isPost()) {
            $login = $this->req->get('login');
            $this->user->login([
                'id' => 1,
                'login' => $login,
                'tt' => time(),
                'roles' => ['user', 'testouser'],
                'name' => 'dfg'
            ], true);
            return $this->redirect('private');
        }
        return [
            'text' => 'login page#####'
        ];
    }

    public function logoutAction()
    {
        $this->user->logout();
        return $this->redirect('');
    }

    public function cliAction(){
        print_r($this->params);
        print_r($GLOBALS);
        return 'END';
    }
}