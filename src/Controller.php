<?php

namespace Yagrysha\MVC;
class Controller
{
	private $req;
	private $res;
	private $app;

	public function __construct(App $app){
		$this->app=$app;
		$this->req=$app->req;
		$this->res = new Response();
	}

	public function run(){

	}
}