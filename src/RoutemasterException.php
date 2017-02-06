<?php

namespace Outlandish\Wordpress\Routemaster;

use Outlandish\Wordpress\Routemaster\Response\RoutemasterResponse;

class RoutemasterException extends \RuntimeException {
	/** @var RoutemasterResponse If present, this will be the response */
	public $response;
	/** @var bool If false, no further route matching will be attempted */
	public $allowFallback = true;
}