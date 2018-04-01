<?php defined('SYSPATH') or die('No direct script access');
class View
{
	const VIEWPATH = APPPATH . DS . 'views';

	protected $_viewdata = array();

	public function render($path_to_view, $layout = FALSE)
	{
		if($layout)
		{
			$viewdata = $this->_viewdata;
			$this->_viewdata = array(
				'subview' => View::make($path_to_view, $viewdata)
			);

			$path_to_view = $layout;
		}

		foreach($this->_viewdata as $varname => $value)
		{
			$$varname = $value;
		}

		$viewloc = self::VIEWPATH . DS .  $path_to_view . EXT;

		if(!file_exists($viewloc))
		{
			throw new Exception('View: ' . $viewloc . ' could not be found');
		}
		else
		{
			require($viewloc);
		}
	}

	public function bind($varname, $value)
	{
		$this->_viewdata[$varname] = $value;

		return $this;
	}

	public static function make($path_to_view, $data = array(), $syspath = FALSE)
	{
		foreach($data as $varname => $value)
		{
			$$varname = $value;
		}

		if($syspath)
		{
			$viewloc = SYSPATH . DS . 'views' . DS . $path_to_view . EXT;
		}
		else
		{
			$viewloc = self::VIEWPATH . DS .  $path_to_view . EXT;
		}

		if(!file_exists($viewloc))
		{
			throw new Exception('View: ' . $viewloc . ' could not be found');
		}
		else
		{
			require($viewloc);
		}
	}
}
