<?php defined('SYSPATH') or die('No direct script access');
/* PHP MVC Lite
 * @copyright 2016 Adam Copley
 * @author Adam Copley
 * @description system/bootstrop.php - nuts and bolts of the application
 * includes the config, class autloader and instantiates the system
 */

/* require core config file */
require_once(ROOT . DS . 'config' . DS . 'config.php');

/* class autloader */
require_once(ROOT . DS . 'system' . DS . 'classes' . DS . 'Autoloader.php');

/* instantiate a new autoloader - now we can load any class in SYSPATH, MODPATH and APPPATH */
$autoloader = new Autoloader();

/* instantiate system components before we send a request to the router */
$system = new System();
