<?php defined('SYSPATH') or die('No direct script access');
class Router
{
    private $_controller;
	private $_controller_instance;
    private $_action;
    private $_path;
    private $_query;
	private $_request;
	private $_response;

	private static $_server;
	private static $_protocol;

	const REQUEST_PATH_STRING = "path";
	const REQUEST_PATH_SEPARATOR = "/";
	const REQUEST_QUERY_STRING = "query";
	const REQUEST_QUERY_SEPARATOR = "&";

	const PATH_CONTROLLER = 1;
	const PATH_ACTION = 2;

	const CONTROLLER_PREFIX = "Controller_";

    public function __construct(Request $request, Response $response)
	{
		self::$_server = $_SERVER['SERVER_NAME'];
		self::$_protocol = Config::get('protocol');

		$this->_request = $request;
		$this->_response = $response;
		$request_uri = $this->_request->uri();

        $this->_path = explode(self::REQUEST_PATH_SEPARATOR, $request_uri[self::REQUEST_PATH_STRING]);

        if(isset($request_uri[self::REQUEST_QUERY_STRING]))
        {
          	$this->_query = explode(self::REQUEST_QUERY_SEPARATOR, $request_uri[self::REQUEST_QUERY_STRING]);
        }

        if(empty($this->path[self::PATH_CONTROLLER]))
        {
          	$this->_controller = str_replace('-', '_', Config::get('default_controller'));
        }
        else
        {
          	$this->_controller = str_replace('-', '_', $this->path[self::PATH_CONTROLLER]);
        }

        if((!isset($this->_path[self::PATH_ACTION])) || empty($this->_path[self::PATH_ACTION]))
        {
          	$this->_action = str_replace('-', '_', Config::get('default_action'));
        }
        else
        {
          	$this->_action = str_replace('-', '_', $this->_path[self::PATH_ACTION]);
        }

		$this->createController();
    }

	public function response()
	{
		return $this->_response;
	}

    private function createController()
	{
      	$this->_controller = self::CONTROLLER_PREFIX . ucwords($this->_controller);

		$this->_controller_instance = new $this->_controller($this->_request, $this->_response, $this->_action, $this->_path, $this->_query);

      	$this->_response->setBody($this->_controller_instance->executeAction());
    }

	public static function get_url($controller, $action, $params = NULL)
	{
		$url = self::$_protocol . '://' . self::$_server . '/' . str_replace('_', '-', $controller) . '/' . str_replace('_', '-', $action);

		if(is_array($params))
		{
			foreach($params as $param)
			{
				$url .= '/' . $param;
			}
		}

		return $url;
	}
}
