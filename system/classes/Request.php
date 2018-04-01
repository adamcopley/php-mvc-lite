<?php defined('SYSPATH') or die('No direct script access');
class Request
{
	const POST = "POST";
	const GET = "GET";
	const PUT = "PUT";
	const DELETE = "DELETE";

	private $_request_method;
	private $_request_uri;

	public function __construct()
	{
		$this->removeMagicQuotes();
		$this->unregisterGlobals();

		$this->_request_uri = parse_url($_SERVER['REQUEST_URI']);
		$this->_request_method = $_SERVER['REQUEST_METHOD'];

		if($this->method() != self::POST)
		{
			return TRUE;
		}
		else
		{
			$this->checkCSRFToken();
		}
	}

	public function uri()
	{
		return $this->_request_uri;
	}

	public function get($key, $val = NULL)
	{
		if($val !== NULL)
		{
			$_GET[$key] = $val;
		}

		return $_GET[$key];
	}

	public function post($key, $val = NULL)
	{
		if($val !== NULL)
		{
			$_POST[$key] = $val;
		}

		return $_POST[$key];
	}

	public function redirect($url)
	{
		header("Location: " . $url);
	}

	public function method()
	{
		return $this->_request_method;
	}

	private function stripSlashesDeep($value)
	{
	    $value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
	    return $value;
	}

	private function removeMagicQuotes()
	{
	  	if(get_magic_quotes_gpc())
		{
	    	$_GET = $this->stripSlashesDeep($_GET);
	    	$_POST = $this->stripSlashesDeep($_POST);
	    	$_COOKIE = $this->stripSlashesDeep($_COOKIE);
	  	}
	}

	private function unregisterGlobals()
	{
	    if(ini_get('register_globals'))
		{
	        $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
	        foreach($array as $value)
			{
	            foreach($GLOBALS[$value] as $key => $var)
				{
	                if($var === $GLOBALS[$key])
					{
	                    unset($GLOBALS[$key]);
	                }
	            }
	        }
	    }
	}

	private function checkCSRFToken()
	{
		$session_token = Session::getCSRFToken();

		$request_token = $this->post('csrf_token');

		if(PML::slow_equals($request_token, $session_token))
		{
			return TRUE;
		}
		else
		{
			throw new Exception('Suspected CSRF Attack');
		}
	}
}
