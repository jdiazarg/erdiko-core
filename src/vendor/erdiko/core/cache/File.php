<?php
/**
 * Cache using the filesystem
 * 
 * @category 	Erdiko
 * @package  	core
 * @copyright 	Copyright (c) 2014, Arroyo Labs, www.arroyolabs.com
 * @author		Varun Brahme
 * @author		John Arroyo, john@arroyolabs.com
 */
namespace erdiko\core\cache;
use erdiko\core\cache\Interface;


class File extends erdiko\core\datasource\File implements CacheInterface 
{
	protected $_fileData = array();

	public function __construct($cacheDir=null)
	{
		if(!isset($cacheDir))
		{
			$rootFolder=dirname(dirname(dirname(__DIR__))); 
			$cacheDir=$rootFolder."/var/cache";
		}
		parent::__construct($cacheDir);
	}

	public function put($key, $data)
	{
		$filename=null;
		if(isset($this->_fileData[(string)$key]))
			$filename=$this->_fileData[(string)$key];
		if(!isset($filename))
			$filename=$key;
		if($this->write($data,$filename))
		{
			$this->_fileData[(string)$key]=$filename;
			return true;
		}
		else
			return false;
	}
	
	public function get($key)
	{
		$filename=null;
		if(isset($this->_fileData[(string)$key]))
			$filename=$this->_fileData[(string)$key];
		if(!isset($filename))
			return false;
		else
			return $this->read($filename);
	}
	
	public function forget($key)
	{
		$filename=null;
		if(isset($this->_fileData[(string)$key]))
			$filename=$this->_fileData[(string)$key];
		if(!isset($filename))
			return false;
		else
		{
			if($this->delete($filename))
			{
				unset($this->_fileData[(string)$key]);
				return true;
			}
			return false;
		}
	}
	
	public function has($key)
	{
		$filename=null;
		if(isset($this->_fileData[(string)$key]))
			$filename=$this->_fileData[(string)$key];
		return(isset($filename) && $this->fileExists($filename));
	}
	
	public function forgetAll()
	{
		$ret=true;
		foreach($this->_fileData as $key => $filename)
			$ret = $ret && $this->forget($filename);
		if($ret)
		{
			$this->_fileData = array();
			return true;
		}
		return false;
	}
}