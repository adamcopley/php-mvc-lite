<?php defined('SYSPATH') or die('No direct script access');
class ExceptionHandler
{
	private $_exception;

	public function __construct()
	{
		$this->_register();
	}

	private function _register()
	{
		set_exception_handler(array($this, 'exceptionHandler'));
	}

	public function exceptionHandler(Exception $exception)
	{
		$this->_exception = $exception;

		switch(APP_ENV){

	      	case 'PRODUCTION':

				Logger::log('exception', $this->_exception->getMessage());

	        	$this->_emailException();

				$this->_gracefulOutput();

	      	break;

	      	case 'STAGING':

	      	case 'DEVELOPMENT':

				$this->_emailException();

				$this->_debugOutput();

				Logger::log('exception', $this->_exception->getMessage());

	      break;
	    }
	}

	private function _debugOutput()
	{
		$data =  $this->_getErrorData();

		View::make('exceptions/exception', $data, TRUE);
	}

	private function _gracefulOutput()
	{
		echo "An error occured. Please reload the page.";
	}

	private function _getErrorData()
	{
		$stacktrace = $this->_exception->getTrace();

		$filepath = PML::recursiveFind('file', $stacktrace);

		$file = file($filepath);

		$line = PML::recursiveFind('line', $stacktrace) - 1;

		$source_code = $file[$line];

		$data = array(
			'message' => $this->_exception->getMessage(),
			//put a leading line break before the stack trace number
			'stacktrace' => preg_replace('/(#[0-9]+)/', "<br />$1", $this->_exception->getTraceAsString()),
			'file' => $filepath,
			'line' => $line,
			'source_code' => $source_code
		);

		return $data;
	}

	private function _emailException()
	{
		

	}
}
