<?php
namespace Yagrysha\MVC\Cache;

/**
 * Class File
 * @package Yagrysha\MVC\Cache
 */
class File {

	private $options = [
		'default_lifetime'=>36000,
		'cache_dir' => null,
		'file_locking' => false,
		'hashed_directory_level' => 0,
		'hashed_directory_perm' => 0700,
		'cache_file_perm' => false,
	];

	public function __construct($options=[]){
		$this->options = array_merge($this->options, $options);
	}

	public function get($key, $lifetime=null){
		$file = $this->getFilePath($key);
		if (!is_file($file)) {
			return null;
		}
		if(empty($lifetime)){
			$lifetime = $this->options['default_lifetime'];
		}
		if (filemtime($file) < (time() - $lifetime)) {
			unlink($file);
			return null;
		}
		return $this->fileGetContents($file);
	}

	public function set($key, $data){
		return $this->filePutContents($this->getFilePath($key), $data);
	}

	public function setSetialize($key, $data){
		if(is_array($key)){
			$key=(empty($key['cacheGroup'])?'':($key['cacheGroup'].DIRECTORY_SEPARATOR)).serialize($key);
		}
		return $this->filePutContents($this->getFilePath($key), serialize($data));
	}

	public function getSetialize($key, $lifetime=null){
		if(is_array($key)){
			$key=(empty($key['cacheGroup'])?'':($key['cacheGroup'].DIRECTORY_SEPARATOR)).serialize($key);
		}
		$ret = $this->get($key, $lifetime);
		return $ret?unserialize($ret):$ret;
	}

	public function delete($key){
		return unlink($this->getFilePath($key));
	}

	public function getFilePath($key){
		$path = $this->options['cache_dir'];
		if($ds = strrpos($key, DIRECTORY_SEPARATOR)){
			$path.= substr($key, 0, $ds+1);
		}
		$key= md5($key);
		if($this->options['hashed_directory_level']>0){
			$level = min(8,(int)$this->options['hashed_directory_level']);
			$start = 0;
			do{
				$path.=substr($key,$start,2).DIRECTORY_SEPARATOR;
				$start+=2;
			}while(--$level);
		}
		return $path.$key;
	}

	protected function fileGetContents($file)
	{
		if($this->options['file_locking']){
			$result = null;
			$f = @fopen($file, 'rb');
			if ($f) {
				@flock($f, LOCK_SH);
				$result = stream_get_contents($f);
				@flock($f, LOCK_UN);
				@fclose($f);
			}
			return $result;
		}
		return file_get_contents($file);
	}

	protected function filePutContents($file, $string)
	{
		$dir = dirname($file);
		if (!is_dir($dir))
			mkdir($dir, $this->options['hashed_directory_perm'], true);
		$result = file_put_contents($file, $string, $this->options['file_locking'] ? LOCK_EX : 0);
		if($this->options['cache_file_perm'])@chmod($file, $this->options['cache_file_perm']);
		return $result;
	}

	public function clearExpired($maxLifeTime, $dir=null){
		$expiredTime = time() - $maxLifeTime;
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			$path = "$dir/$file";
			if (is_dir($path)) {
				$this->clearExpired($path);
			}elseif(filemtime($path) < $expiredTime){
				unlink($path);
			}
		}
	}

	public function clearEmptyDir($dir=null){
		$dir = null==$dir?$this->options['cache_dir']:$dir;
		$files = array_diff(scandir($dir), array('.','..'));
		if($files) {
			foreach ($files as $file) {
				$path = "$dir/$file";
				if (is_dir($path)) {
					$this->clearEmptyDir($path);
				}
			}
		}elseif($dir!=$this->options['cache_dir']){
			return rmdir($dir);
		}
	}

	public function clearDir($dir){
		return $this->delTree($this->options['cache_dir'].$dir);
	}

	public function deleteAll(){
		$dir = $this->options['cache_dir'];
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
		}
	}

	public function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}


}