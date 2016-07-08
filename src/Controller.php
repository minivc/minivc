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
