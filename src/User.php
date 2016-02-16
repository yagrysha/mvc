<?php
/**
 * @author Yaroslav Gryshanovich <yagrysha@gmail.com>
 */

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
        'roles' => ['guest']
    ];

    protected $isLogged = false;

    public static function getUser(Request $req)
    {
        if (null == self::$instance) {
            self::$instance = new static($req);
        }

        return self::$instance;
    }

    private function __construct(Request $req)
    {
        $user = $req->session(static::SESSIONNAME);
        if ($user) {
            $this->userData = $user;
        } else {
            $code = $req->cookie(static::REMEMBER_COOKIENAME);
            if ($code) {
                $this->userData = $this->getUserDataByCode($code);
            }
        }
        if (empty($this->userData['id'])) {
            $this->userData = $this->defUserData;
        }else{
            $this->isLogged = true;
        }
        $this->userData['ip'] = $req->ip;
        $this->userData['tm'] = time();
        $this->setSession();
    }

    public function setSession()
    {
        $_SESSION[static::SESSIONNAME] = $this->userData;
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
                if (in_array($r, $this->userData['roles'], true)) {
                    return true;
                }
            }
            return false;
        }
        return in_array($role, $this->userData['roles'], true);
    }

    public function getUserDataByCode($code)
    {
        // get user data from database by $code and generate new code
        $this->login($this->defUserData);
        return $this->defUserData;
    }

    /**
     * set user date to session
     * @param array $userData
     * @param null|string $rememberCode
     */
    public function login($userData, $rememberCode = null)
    {
        session_regenerate_id();
        $this->userData = $userData;
        if (isset($_SESSION[static::SESSIONNAME])) {
            $userData = array_merge($_SESSION[static::SESSIONNAME], $userData);
        }
        $_SESSION[static::SESSIONNAME] = $userData;
        $this->isLogged = true;
        if ($rememberCode) {
            setcookie(
                static::REMEMBER_COOKIENAME,
                $rememberCode,
                time() + 86400 * static::REMEMBER_DAYS,
                '/',
                App::config('host'),
                false,
                true
            );
        }
    }

    public function logout()
    {
        session_regenerate_id();
        $this->isLogged = false;
        unset($_COOKIE[static::REMEMBER_COOKIENAME]);
        $_SESSION[static::SESSIONNAME] = $this->defUserData;
        setcookie(static::REMEMBER_COOKIENAME, '', time() - 100000, '/', App::config('host'), false, true);
    }

    public function getData()
    {
        return $this->userData;
    }

    public function getId(){
        return $this->userData['id'];
    }
}