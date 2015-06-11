<?php
namespace Yagrysha\MVC;
class AppTest extends \PHPUnit_Framework_TestCase
{
	protected static  $app;
    public static function setUpBeforeClass()
    {
		echo "\nstart ".__CLASS__."\n";
		self::$app = new App();
    }

    static public function tearDownAfterClass()
    {
    }

    public function testRun(){
        $this->assertInstanceOf('Yagrysha\MVC\App', self::$app);
        $this->assertEquals('dev', self::$app->env);
		self::$app->run();
    }

    public function testAccess(){
        $params = self::$app->checkRoute('/admin');
        $this->assertEquals('default', $params['controller']);
        $this->assertEquals('admin', $params['action']);

        $params = self::$app->checkRoute('/admin/test');
        $this->assertEquals('admin', $params['controller']);
        $this->assertEquals('test', $params['action']);

        $params = self::$app->checkRoute('/user/list/123');
        $this->assertEquals('user', $params['controller']);
        $this->assertEquals('list', $params['action']);
        $this->assertEquals('123', $params['data']);
    }
}