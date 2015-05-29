<?php

namespace Yagrysha\MVC;
class User
{
	const REMEMBER_DAYS = 60;
	const REMEMBER_COOKIENAME = '__seu';
	const SESSIONNAME = '__user';
	const ROLE_GUEST = 0;
	const ROLE_USER = 1;
	const ROLE_ADMIN = 2;


	private static $instance;
	private $userData;
	private $defUserData=[
		'id'=>0,
		'roles'=>[self::ROLE_GUEST]
	];

	private function __construct(Request $req){
		$user = $req->session(self::SESSIONNAME);
		if($user){
			$this->userData = $user;
		}else{
			$remembercode = $req->cookie(self::REMEMBER_COOKIENAME);
			if($remembercode){
				$this->userData = $this->getUserDataByCode($remembercode);
			}
		}
		if(!$this->userData) {
			$this->userData = $this->defUserData;
		}
		$_SESSION[self::SESSIONNAME]['ip'] = $req->ip;
		$_SESSION[self::SESSIONNAME]['tm'] = time();
		//todo проверка, привязка к ip,  обновление по времени
	}

	static public function getUser(Request $req){
		if(null==self::$instance){
			self::$instance = new self($req);
		}
		return self::$instance;
	}

	public function isLogged(){
		return !empty($this->userData['id']);
	}

	public function hasRole($role){
		return in_array($role, $this->userData['roles']);
	}

	public function destroySession(){
		unset($_COOKIE[self::REMEMBER_COOKIENAME]);
		$_SESSION = [];
		session_destroy();
		setcookie(self::REMEMBER_COOKIENAME, '', time() - 100000, '/');
	}

	public function getUserDataByCode($code){
		//get from base
		$this->login($this->defUserData, 'genme');
		return $this->defUserData;
	}

	public function login($userData, $rememberCode=null){
		$this->userData=$userData;
		$_SESSION[self::SESSIONNAME] = array_merge($_SESSION[self::SESSIONNAME], $userData);
		if($rememberCode){
			setcookie(self::REMEMBER_COOKIENAME, $rememberCode, time() + 86400 *self::REMEMBER_DAYS, '/', HOST, false, true);
		}
	}
}