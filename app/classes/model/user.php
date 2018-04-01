<?php
class Model_User extends Model
{
	protected $_table = 'users';

	public function verify($password)
	{
		if(password_verify($password, $this->password))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function find_by_email($email)
	{
		return $this->where('', 'email', '=', $email)->find();
	}

}
