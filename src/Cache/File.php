<?php
namespace Yagrysha\MVC\Cache;


class File {

	const DEF_LIFETIME = 3600;
	private $options = [
		'cache_dir' => null,
		'file_locking' => false,
		'hashed_directory_level' => 0,
		'hashed_directory_perm' => false,
		'cache_file_perm' => false,
		'metadatas_array_max_size' => 100
	];


	public function __construct($options=[]){
		$this->options = array_merge($this->options, $options);
	}

	public function get($key, $lifetime){
		return null;
	}

	public function set($key, $data){
		return true;
	}
	public function setSetialize($key, $data){
		return true;
	}

	public function clean(){
		return true;
	}

	protected function fileGetContents($file)
	{
		if($this->options['file_locking']){
			$result = false;
			if (!is_file($file)) {
				return false;
			}
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
		$result = file_get_contents($file, $string, $this->options['file_locking'] ? LOCK_EX : 0);
		if($this->options['cache_file_perm'])@chmod($file, $this->options['cache_file_perm']);
		return $result;
	}

	public function clearExpired($maxLifeTime){

	}
}