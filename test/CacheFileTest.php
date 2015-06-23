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

	public function testPAth(){
		$cache = new File(['hashed_directory_level'=>1]);
		$key = 'test_key';
		$mkey = md5($key);
		$this->assertEquals(substr($mkey,0,2).DIRECTORY_SEPARATOR.$mkey, $cache->getFilePath($key));

		$key = 'dir/test_key';
		$mkey = md5($key);
		$this->assertEquals('dir/'.substr($mkey,0,2).DIRECTORY_SEPARATOR.$mkey, $cache->getFilePath($key));
	}
	public function testSet(){
		$cache = new File(['hashed_directory_level'=>1,'cache_dir'=>__DIR__.'/resources/']);
		$cache->set('key', 'string');
		$this->assertEquals($cache->get('key'), 'string');
		$cache->setSetialize('dir/key', [2=>'string']);
		$this->assertEquals($cache->get('dir/key'), serialize([2=>'string']));

	}
}