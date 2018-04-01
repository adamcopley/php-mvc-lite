<?php defined('SYSPATH') or die('No direct script access');
class Session
{
	const PROP_SEPARATOR = ".";
	const AUTH_DATA_KEY = "authdata";

	private static $_csrf_token;

	public static function init()
	{
		$salt = Config::get('csrf_salt');

		$userdata = $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'];

		$combine = $salt . $userdata;

		static::$_csrf_token = hash('sha1', $combine);

		static::set('authdata', array());
	}

	public static function getCSRFToken()
	{
		return static::$_csrf_token;
	}

	public static function get($property = '')
	{
		$keys = explode(static::PROP_SEPARATOR, $property);

		$data = $_SESSION;

		foreach($keys as $key)
		{
			$data = $data[$key];
		}

		return $data;
	}

	public static function set($property , $value = NULL)
	{
		$keys = explode(static::PROP_SEPARATOR, $property);

		static::_setProperty($_SESSION, $keys, $value);

		return $value;
	}

	public static function _unset($property)
	{
		$keys = explode(static::PROP_SEPARATOR, $property);

		static::_unsetProperty($_SESSION, $keys, $property);

		return TRUE;
	}

	public static function setAuthData($key, $data = NULL)
	{
		static::set(static::AUTH_DATA_KEY . static::PROP_SEPARATOR . $key, $data);
	}

	public static function unsetAuthData($key)
	{
		static::_unset(static::AUTH_DATA_KEY . static::PROP_SEPARATOR . $key);
	}

	private static function _unsetProperty(&$data, array $keys, $value)
	{
		$current = &$data;

		$i = 0;

  		foreach($keys as $key)
		{
			if($i == count($keys) - 1)
			{
				unset($current[$key]);

				return TRUE;
			}

    		$current = &$current[$key];

			$i++;
  		}
	}

	private static function _setProperty(&$data, array $keys, $value)
	{
		$current = &$data;

  		foreach($keys as $key)
		{
    		$current = &$current[$key];
  		}

  		$current = $value;
	}
}
