<?php
/* PHP MVC Lite
 * @copyright 2016 Adam Copley
 * @author Adam Copley
 * @description public/index.php - entry point for application
 */
session_start();

/* shortcut for directory separator */
define('DS', DIRECTORY_SEPARATOR);

/* default file extension */
define('EXT', '.php');

/* key application directories */
define('ROOT', dirname(dirname(__FILE__)));
define('SYSPATH', ROOT . DS . 'system');
define('MODPATH', ROOT . DS . 'modules');
define('APPPATH', ROOT . DS . 'app');
define('LOGPATH', ROOT . DS . 'logs');

/* get application environment from environment variables */
define('APP_ENV', $_SERVER['APPLICATION_ENVIRONMENT']);

/* @require bootstrap.php - the nuts and bolts of the application */
require_once(ROOT . DS . 'system' . DS . 'bootstrap.php');

/* create a new request and send it the router */
Session::init();
$router = new Router(new Request(), new Response());
$router->response()
	   ->sendBody();
