<?php

namespace Outlandish\Wordpress\Routemaster\Response;

class HtmlResponse extends TemplatedResponse
{
	public static $defaultLayout = 'layout';

	public function __construct($outputArgs = [])
	{
		parent::__construct($outputArgs);
		$this->layout = self::$defaultLayout;
	}
}