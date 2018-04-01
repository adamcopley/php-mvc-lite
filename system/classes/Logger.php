<?php defined('SYSPATH') or die('No direct script access');
class Logger
{
	const LOGDIR = ROOT . DS . 'logs';
	const LOGEXT = ".log";
	const LOG_CHMOD = "775";
	const LOG_NEWLINE = "\r\n";
	const LOG_WRITEMODE = "a";

	private static $_file;
	private static $_filename;
	private static $_dir;
	private static $_fullpath;

	protected static function _init($type)
	{
		static::_openFile($type);
	}

	public static function log($type, $msg)
	{
		static::_init($type);

		$msg = date("Y-m-d H:i:s") . " - " . $msg . static::LOG_NEWLINE;

		static::_write($msg);

		static::_closeFile();
	}

	protected static function _openFile($type)
	{
		static::$_dir = static::LOGDIR . DS . $type;
		static::$_filename = $type . " - " . date("Ymd") . static::LOGEXT;
		static::$_fullpath = static::$_dir . DS . static::$_filename;

		if(!file_exists(static::$_dir))
		{
			if(!mkdir(static::$_dir, static::LOG_CHMOD, TRUE))
			{
				throw new Exception('Could not create directory: ' . static::$_dir);
			}
		}
		static::$_file = fopen(static::$_fullpath, static::LOG_WRITEMODE);
	}

	protected static function _closeFile()
	{
		static::$_filename = NULL;
		static::$_dir = NULL;
		if(static::$_file)
		{
			static::$_file = fclose(static::$_file);
		}
	}

	protected static function _write($msg)
	{
		fwrite(static::$_file, $msg);
	}
}
