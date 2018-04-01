<?php
class Controller_Auth extends Controller
{
  	public function action_index()
  	{
		$this->view->bind('message', '');
		$this->view->render('login');
  	}

	public function action_login()
	{
		if($this->request->method() !== Request::POST)
		{
			throw new Exception('POST Only to login');
		}

		$user = Model::factory('user')->find_by_email($_POST['email']);

		if(!$user->verify($_POST['password']))
		{
			$message = 'Unable to verify details. Please try again';
			$this->view->bind('message', $message);
			$this->view->render('login');
		}
		else
		{
			Session::set('user', $user);
			$this->view->render('login', 'layouts/main');
		}
	}
}
