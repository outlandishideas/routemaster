<?php

namespace Outlandish\Wordpress\Routemaster;

use Outlandish\Wordpress\Routemaster\Response\HtmlResponse;

class RouterHelper
{
	/**
	 * Routes use this when creating a response
	 * @param array|object $args
	 * @return HtmlResponse
	 */
	public function createDefaultResponse($args = [])
	{
		return new HtmlResponse($args);
	}

	public function isPost() {
		return strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';
	}

	public function isGet() {
		return strtoupper($_SERVER['REQUEST_METHOD']) == 'GET';
	}

	public function isPut() {
		return strtoupper($_SERVER['REQUEST_METHOD']) == 'PUT';
	}

	public function getRequestBody() {
		$body = file_get_contents('php://input');
		return $body ? json_decode($body) : null;
	}
}