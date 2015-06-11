<?php
namespace Yagrysha\MVC;
use Yagrysha\MVC\Cache\File;
class CacheFileTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		echo "\nstart ".__CLASS__."\n";
    }

    static public function tearDownAfterClass()
    {
    }

    public function testIndex(){
     	$cache = new File();
		$this->assertInstanceOf('Yagrysha\MVC\Cache\File', $cache);
    }
}