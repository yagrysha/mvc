<?php
namespace Yagrysha\MVC;
class RequestTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		echo "\nstart ".__CLASS__."\n";
    }

    static public function tearDownAfterClass()
    {
    }

    public function testIndex(){
        $req = new Request('test/test', [
            'td1'=>'d1'
        ]);
        $this->assertEquals('test/test', $req->getRequestUri());
        $this->assertEquals('d1', $req->td1);
        $this->assertTrue(isset($req->td1));

        $_SERVER['HTTP_X_REQUESTED_WITH']='XMLHttpRequest';
        $this->assertTrue($req->isXmlHttpRequest());

        $req->setParam('a', 1);
        $this->assertEquals(1,$req->a);

		$this->assertEquals(1, $req->env('q',1));
		$this->assertEquals(1, $req->cookie('q',1));
		$this->assertEquals(1, $req->server('q',1));

		$this->assertInstanceOf('Yagrysha\MVC\User', $req->user);
    }

	public function testIp(){
		$_SERVER['REMOTE_ADDR'] = '1.2.3.4';
		$req = new Request();
		$this->assertEquals($_SERVER['REMOTE_ADDR'], $req->ip);

		$_SERVER['HTTP_X_FORWARDED_FOR'] = '1.2.3.4';
		$req = new Request();
		$this->assertEquals($_SERVER['HTTP_X_FORWARDED_FOR'], $req->ip);

		$_SERVER['HTTP_X_REAL_IP'] = '1.2.3.4';
		$req = new Request();
		$this->assertEquals($_SERVER['HTTP_X_REAL_IP'], $req->ip);
	}

	public function testGet(){
		$req = new Request();
		$_GET['test'] =1;
		$this->assertEquals($_GET['test'], $req->test);
		$this->assertTrue(isset($req->test));
		$this->assertFalse($req->isPost());
		$this->assertFalse($req->isXmlHttpRequest());
	}

	public function testPost(){
		$req = new Request();
		$_POST['test'] =1;
		$this->assertEquals($_POST['test'], $req->test);
		$this->assertTrue(isset($req->test));

		$this->assertFalse($req->isPost());
		$_SERVER['REQUEST_METHOD']='POST';
		$this->assertTrue($req->isPost());

		$this->assertEquals($_SERVER['REQUEST_METHOD'], $req->server('REQUEST_METHOD'));
	}
}