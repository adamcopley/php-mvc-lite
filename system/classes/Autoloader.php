<?php defined('SYSPATH') or die('No direct script access');
/* PHP MVC Lite
 * @copyright 2016 Adam Copley
 * @author Adam Copley
 * @description system/classes/Autoloader.php - registers the autoloader function
 * so developer can instantiate classes from SYSPATH MODPATH AND APPPATH
 */
class Autoloader
{
	/**
	 *
	 * directories to search for classes
	 * @var array
	 */
	private $_directories = array(
						APPPATH,
						SYSPATH
					);

	/**
	 *
	 * paths to display in exception if class can't be found
	 * @var array
	 */
	private $_not_found_paths = array();


	public function __construct()
	{
		$this->_register();
	}

	/**
	 *
	 * _register
	 * @return void register autoloader function
	 */
	private function _register()
	{
		spl_autoload_register(array($this, 'loadClass'));

		//composer dependencies autoloader
		require_once(ROOT . DS . 'vendor' . DS . 'autoload.php');
	}

	/**
	 *
	 * loadClass - function to autoload classes from chosen directories
	 * @param  string $className name of the class which is trying to be instantiated
	 * @return bool
	 *
	 * @throws Exception
	 */
	public function loadClass($className)
	{
		$this->_not_found_paths = array();

	  	$array = explode('_', $className);

		foreach($this->_directories as $dir)
		{
			$path = $dir . DS . 'classes';

			foreach($array as $part)
		  	{
		    	$path .= DS;

				if($dir == SYSPATH)
				{
					$path.= ucwords($part);
				}
				else
				{
					$path .= strtolower($part);
				}
		  	}

		  	$path .= EXT;

			if(file_exists($path))
			{
		    	require_once($path);

				return TRUE;
		  	}
			else
			{
				$this->_not_found_paths[] = $path;
			}
		}

		throw new Exception('Class: ' . $className . 'could not be found in any of the following paths: <br>' . implode('<br>', $this->_not_found_paths));
	}
}
