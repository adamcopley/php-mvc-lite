<?php defined('SYSPATH') or die('No direct script access');
class DB
{
	private static $_instance = NULL;

	private static $_result;

	const SELECT = 1;
	const INSERT = 2;
	const UPDATE = 3;
	const DELETE = 4;

	private static function _init()
	{
		if(!static::$_instance instanceof Database)
		{
			static::$_instance = new Database();
		}

		return static::$_instance;
	}

	public static function query($type, $sql, $params = NULL)
	{
		static::_init();

		switch($type)
		{
			case static::SELECT:

				static::$_result = static::$_instance->query($sql)->fetch_array_all($params);

			break;

			case static::INSERT:
			case static::UPDATE:
			case static::DELETE:

				static::$_result = static::$_instance->query($sql)->execute($params);

			break;
		}

		return static::$_result;
	}

	public static function getColumnNames($table)
	{
		static::_init();

		static::$_result = static::$_instance->getColumnNames($table);

		return static::$_result;
	}
}
