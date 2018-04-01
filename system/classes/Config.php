<?php defined('SYSPATH') or die('No direct script access');
class Config
{
	private static $_config = NULL;
	private static $_initialized = FALSE;

	const PROP_SEPARATOR = ".";

	private static function _init()
	{
		if(self::$_initialized == TRUE)
		{
			return;
		}

		static::$_config = $GLOBALS['config'];

		unset($GLOBALS['config']);

		self::$_initialized = TRUE;
	}

	public static function get($property = '')
	{
		self::_init();

		$keys = explode(self::PROP_SEPARATOR, $property);

		$data = static::$_config;

		foreach($keys as $key)
		{
			$data = $data[$key];
		}

		return $data;
	}

	public static function set($property , $value = '')
	{
		self::_init();

		$keys = explode(self::PROP_SEPARATOR, $property);

		self::_setProperty(static::$_config, $keys, $value);

		return $value;
	}

	private static function _setProperty(&$config, array $keys, $value)
	{
		$current = &$config;

  		foreach($keys as $key)
		{
    		$current = &$current[$key];
  		}

  		$current = $value;
	}
}
