<?php
namespace Yagrysha\MVC;
class AppTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		echo "\nstart ".__CLASS__."\n";
    }

    static public function tearDownAfterClass()
    {
    }

    public function testRun(){
        $app = new App();
        $this->assertInstanceOf('Yagrysha\MVC\App', $app);
        $this->assertEquals('dev', $app->env);
        $app->run();
    }

    public function testAccess(){
        $app = new App();
        $params = $app->checkRoute('admin');
        $this->assertEquals('admin', $params['controller']);
        $this->assertEquals('index', $params['action']);
        $params = $app->checkRoute('admin/test');
        $this->assertEquals('admin', $params['controller']);
        $this->assertEquals('test', $params['action']);
        $params = $app->checkRoute('user/list/123');
        $this->assertEquals('user', $params['controller']);
        $this->assertEquals('list', $params['action']);
        $this->assertEquals('123', $params['data']);
    }
}