<?php defined('SYSPATH') or die('No direct script access');
class System
{
	private $_exception_handler;

	const DEFAULT_TIMEZONE = "Europe/London";

	public function __construct()
	{
		$this->_setReporting();

		$this->_setLogging();

		$handler = $this->_setExceptionHandling();

		$this->_exception_handler = $handler;

		Cookie::$salt = Config::get('cookie_salt');
	}

	private function _setExceptionHandling()
	{
		return new ExceptionHandler();
	}

	private function _setReporting()
	{
	  	if (APP_ENV == 'DEVELOPMENT' || APP_ENV == 'STAGING')
		{
	    	error_reporting(E_ALL);
	    	ini_set('display_errors','On');
	  	}
		else
		{
	    	error_reporting(E_ALL);
	    	ini_set('display_errors','Off');
	  	}
	}

	private function _setLogging()
	{
		ini_set('log_errors', 'On');
		ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
	}

	private function _setLocale()
	{
		date_default_timezone_set(self::DEFAULT_TIMEZONE);
	}
}
