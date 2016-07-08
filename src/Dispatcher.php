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

class Dispatcher
{
	private $path;
	private $routes;

	public function __construct($routes = array())
	{
		$cut = strlen(dirname($_SERVER['SCRIPT_NAME']));
		$uri = substr($_SERVER['REQUEST_URI'], $cut);

		$this->path   = trim(strtok($uri, '?'), '/');
		//$this->path   = trim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
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
