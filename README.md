# PHP MVC Lite #

## Overview ##

A simple and light MVC framework with a simple structure inspired by Kohana Framework.

The main objectives of this framework are to:

* Remove the need to explicitly define routes & urls
* Provide some base classes required for any small project
* An easy to use object oriented structure and API
* Simple database abstraction

## Installation ##

* Clone the repo
* Create a new virtualhost on you dev box with the base URL pointing to to php-mvc-lite/public
* Go to your new URL
* See what's happening in app/classes/controller/demo.php

## Routes ##

The URL format of any route follows this pattern:

URL Format:   http://domain.com/<controller>/<action>/<param>/<param>/<param>

### Controller ###

The controllers sit in the app/classes/controller directory.
The conventions for controller classes are:

1. All controller classes must be derived from the base class Controller

2. Always prepend with 'Controller_'

3. Use Underscore as directory separator for example:
	- Class Controller_Admin_Controlpanel would be in the directory app/classes/controller/admin/controlpanel.php
	- Class Controller_Demo would be in directory app/classes/controller/demo.php

4. The directory structure, naming structure and inheritance structure should all match. e.g:
	 - Controller_Admin_Controlpanel extends Controller_Admin which extends Controller

5. To access the controller in the URL use hyphens to separate directories for example:
	- URL http://domain.com/admin-controlpanel will use Controller_Admin_Controlpanel
	- URL http://domain.com/contact will use Controller_Contact


### Actions ###

All action methods within controllers must be prepended with 'action_', and also follow the similar naming conventions
as controllers. index as the default action if none is specified

- To access the method action_process within the class Controller_Shop_Order you would use http://domain.com/shop-order/process
- All action methods must be public

### Views ###

Views sit in the app/views directory and are referenced by their absolute path within the views directory.
You don't need to write the .php file extension in the path parameter, so an example of a view called main.php in the root of the views directory
you would pass just pass `'main'` as the path to view parameter, similarly if it was in views/layouts/main you would pass `'layouts/main'`.

1. Rendering:

 - To render a view you can either use

  			`$this->view->render('path/to/view');`

	from within any controller which has extended the base controller.

2. Binding Data:
 - You can also bind data to views which will be declared as variables within the rendered view's scope by calling:

  			`$this->view->bind('varname', 'value');`

 - The bind methods are chainable so you can bind multiple variables in a single call with:

 			`$this->view->bind('var1', 'val1')->bind('var2', 'val2')->render('path/to/view');`

3. Rendering a view from within a view:
 - To render a another view within a view you can simply instantiate a new view class with the new keyword:

 			`$subview = new View();
			 $subview->bind('var1', 'val1')
			 		 ->render('path/to/subview');`

 - Or alternatively call a view with the static method 'make':

			`View::make('path/to/view');`

 - You can also bind data by passing an array as the 2nd parameter:

			`View::make('path/to/view', array(
										'var1' => 'val1',
										'var2' => 'val2'
										)
				);`

### Putting it together ###

See the documentation for full details on the public API of the framework
