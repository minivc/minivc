<?php
/**
 * Minivc - Minimalistic view-controller framework for PHP
 *
 * Copyright (c) 2011-2016 Everaldo Canuto <everaldo.canuto@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

// Register autoload function
spl_autoload_register(function ($classname) {
	$filename = str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php';
	//if (file_exists($filename))
		require $filename;
});

class Router extends ArrayObject
{
	public function append($values)
	{
		foreach ($values as $key => $value)
			$this[$key] = $value;
	}
}

class Dispatcher
{
	private $path;
	private $routes;

	public function __construct($routes = array())
	{
		$this->path   = trim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
		$this->routes = $routes;
	}

	public function dispatch()
	{
		// Searches routes array matching the url path
		foreach ($this->routes as $key => $value) {
			// convert route params into valid regex
			$regex = preg_replace('<@(\w+)>', '(?<$1>[\w-]+)', $key);
			$match = preg_match("<^$regex$>", $this->path, $matches);
			if ($match)
				break;
		}

		// If match, evaluate the route, otherwise use default route
		if ($match) {
			$route = preg_replace_callback('|@([\w\d-]+)|',
				function ($keys) use (&$matches) {
					return @$matches[$keys[1]];
				},
				$value
			);
		} else {
			$route = 'error/404';
		}

		// Split parts (file, class, action)
		$parts = explode('->', $route);
		$controller_file   = $parts[0];
		$controller_action = (count($parts) > 1 ? $parts[1] : 'index') . 'Action';
		$controller_class  = basename($parts[0]);

		// Parameters
		$params = array_merge($matches, $_GET);

		// Dispatch controller
		require "$controller_file.php";

		Controller::dispatch(
			$controller_class,
			$controller_action,
			$params
		);
	}
}

class Controller
{
	protected $params;

	public static function dispatch($class, $action, $params)
	{
		$controller = new $class();
		$controller->params = (object) $params;
		$controller->initialize();
		$controller->$action();
	}

	public function __construct()
	{
		// Nothing to do
	}

	public function initialize()
	{
		// Nothing to do
	}

	function render($name, $data = NULL)
	{
		header("Cache-Control: s-maxage=900, max-age=0");
		$view = new View($name);
		$view->render($data);
	}

	public function redirect($url)
	{
		header("Location: $url");
		die;
	}
}

class View
{
	private $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	private static $settings = [];

	public static function setup($settings)
	{
		static::$settings = $settings;
	}

	public function render($params = NULL)
	{
		if (array_key_exists('globals', static::$settings))
			extract(static::$settings['globals']);

		if ($params !== NULL)
			extract($params, EXTR_OVERWRITE);

		$base_path = isset(static::$settings['base_path']) ?
			static::$settings['base_path'] : 'application/views';

		$viewname = $this->name;

		require  "$base_path/$this->name.phtml";
	}
}
