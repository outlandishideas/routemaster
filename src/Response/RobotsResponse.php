<?php

namespace Outlandish\Wordpress\Routemaster\Response;

class RobotsResponse extends RoutemasterResponse
{
	protected function render()
	{
		do_action('do_robots');
	}

}