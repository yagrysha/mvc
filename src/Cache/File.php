<?php
namespace Yagrysha\MVC\Cache;

/**
 * Class File
 * @package Yagrysha\MVC\Cache
 */
class File {

	private $options = [
		'defaault_lifetime'=>36000,
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
		return $this->filePutContents($this->getFilePath($key), serialize($data));
	}

	public function del($key){
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

	public function clearExpired($maxLifeTime){
//todo
	}

	public function clearEmptyDir(){
		//todo
	}

	public function deleteAll(){
		//todo весь кеш удалить
	}

}