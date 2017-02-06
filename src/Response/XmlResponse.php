<?php

namespace Outlandish\Wordpress\Routemaster\Response;

class XmlResponse extends TemplatedResponse
{
	public function __construct(array $outputArgs)
	{
		parent::__construct($outputArgs);
		$this->headers[] = 'Content-Type: application/xml';
	}

	protected function createView()
	{
		$view = parent::createView();
		$view->logDebug = false;
		return $view;
	}
}