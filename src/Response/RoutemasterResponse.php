<?php

namespace Outlandish\Wordpress\Routemaster\Response;

abstract class RoutemasterResponse
{
	public $headers = [];
	public $outputArgs = [];
	public $routeName = null;

	public function __construct($outputArgs = [])
	{
		$this->outputArgs = $outputArgs;
	}

	/**
	 * Sets the name of the route that generated this response
	 * @param string $routeName
	 */
	public function setRouteName($routeName)
	{
		$this->routeName = $routeName;
	}

	final function handleRequest()
	{
		$this->preRender();
		$this->render();
		$this->postRender();
		exit;
	}

	protected function preRender()
	{
		foreach ($this->headers as $header) {
			if (is_array($header)) {
				header($header[0], $header[1], $header[2]);
			} else {
				header($header);
			}
		}
	}

	protected function render()
	{
		/* do nothing by default */
	}

	protected function postRender()
	{
		/* do nothing by default */
	}
}