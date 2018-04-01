<?php
define('APP_VERSION', '0.0.1');

$base_config = array(
	'APP_VERSION' => APP_VERSION,
	'modules' => array(
		'PHPMailer' => array(
			'files' => array(
				'autoloader' => MODPATH . DS . 'PHPMailer' . DS . 'PHPMailerAutoload' . EXT,
			),
		),
	),
	'core_modules' => array(),
	'default_controller' => 'auth',
	'default_action' => 'index',
	'default_layout' => '',
	'cookie_salt' => 'f73h84fne4/.tt83j$$£^T&6kujbnti8ihuf4',
	'csrf_salt' => '3475yujbvdkjnvoirjoewifnw&^*GIug86uryvifw',
	'protocol' => 'http',
	'admin_email' => 'admin@mvclite.local',


);

switch(APP_ENV)
{
	case "STAGING":

		$server_config = array(
			'database' => array(
				'dsn' => 'mysql:host=localhost;dbname=mvclite',
				'user' => 'adam',
				'pass' => 'secret'
			)
		);
	break;
}

$config = array_merge($base_config, $server_config);
unset($base_config);
unset($server_config);
