<?php

namespace Outlandish\Wordpress\Routemaster\Model;

use Outlandish\Wordpress\Routemaster\Router;

class Route
{
	/** @var string */
	public $pattern;
	/** @var string */
	public $actionName;
	/** @var Router */
	public $handler;

	public function __construct($pattern, $actionName, $handler)
	{
		$this->pattern = $pattern;
		$this->actionName = $actionName;
		$this->handler = $handler;
	}


}