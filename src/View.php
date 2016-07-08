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
