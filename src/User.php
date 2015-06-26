<?php

namespace Yagrysha\MVC;

class User
{
    const REMEMBER_DAYS = 60;
    const REMEMBER_COOKIENAME = '__seu';
    const SESSIONNAME = '__user';

    private static $instance;
    private $userData;
    private $defUserData = [
        'id' => 0,
        'roles' => [ROLE_GUEST]
    ];
    private $isLogged = false;

    private function __construct(Request $req)
    {
        $user = $req->session(self::SESSIONNAME);
        if ($user) {
            $this->userData = $user;
        } else {
            $remembercode = $req->cookie(self::REMEMBER_COOKIENAME);
            if ($remembercode) {
                $this->userData = $this->getUserDataByCode($remembercode);
            }
        }
        if (!$this->userData) {
            $this->userData = $this->defUserData;
        }
        $this->userData['ip'] = $req->ip;
        $this->userData['tm'] = time();
        $this->setSession();
        //todo проверка, привязка к ip,  обновление по времени
    }

    static public function getUser(Request $req)
    {
        if (null == self::$instance) {
            self::$instance = new self($req);
        }

        return self::$instance;
    }

    public function setSession()
    {
        $_SESSION[self::SESSIONNAME] = $this->userData;
    }

    public function isLogged()
    {
        return $this->isLogged;
    }

    /**
     * @param mixed|array $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            foreach ($role as $r) {
                if (in_array($r, $this->userData['roles'])) {
                    return true;
                }
            }

            return false;
        }

        return in_array($role, $this->userData['roles']);
    }

    public function getUserDataByCode($code)
    {
        // get user data from base by $code
        $this->login($this->defUserData, 'newgencode');
        return $this->defUserData;
    }

    public function login($userData, $rememberCode = null)
    {
        $this->userData = $userData;
        $_SESSION[self::SESSIONNAME] = array_merge($_SESSION[self::SESSIONNAME], $userData);
        $this->isLogged = true;
        if ($rememberCode) {
            setcookie(
                self::REMEMBER_COOKIENAME,
                $rememberCode,
                time() + 86400 * self::REMEMBER_DAYS,
                '/',
                HOST,
                false,
                true
            );
        }
    }

    public function logout()
    {
        $this->isLogged = false;
        unset($_COOKIE[self::REMEMBER_COOKIENAME]);
        $_SESSION[self::SESSIONNAME] = $this->defUserData;
        session_regenerate_id();
        setcookie(self::REMEMBER_COOKIENAME, '', time() - 100000, '/', HOST, false, true);
    }

    public function getData()
    {
        return $this->userData;
    }
}