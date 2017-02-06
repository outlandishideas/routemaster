<?php

namespace Outlandish\Wordpress\Routemaster\Response;

class RedirectResponse extends RoutemasterResponse
{
	public function __construct($redirect, $status = 302)
	{
		parent::__construct();
		$this->headers[] = ['Location: ' . $redirect, true, $status];
	}

}