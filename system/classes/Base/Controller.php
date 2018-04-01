<?php defined('SYSPATH') or die('No direct script access');
abstract class Base_Controller
{
    protected $_action;
    protected $_path;
    protected $_query;

	public $request;
	public $response;
	public $view;

	const ACTION_PREFIX = "action_";

    public function __construct(Request $request, Response $response, $action, $path, $query = "")
	{
        $this->_action = $action;
        $this->_path = $path;
        $this->_query = $query;
		$this->request = $request;
		$this->response = $response;
		$this->view = new View();
		$this->view->bind('csrf_token', Session::getCSRFToken());
    }

	protected function before()
	{

	}

	protected function after()
	{

	}

    public function executeAction()
	{
		$this->before();

        $this->{self::ACTION_PREFIX . $this->_action}();

		$this->after();
    }
}
