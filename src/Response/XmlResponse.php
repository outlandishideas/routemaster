<?php

namespace Outlandish\Wordpress\Routemaster\Response;

use Outlandish\Wordpress\Routemaster\View\Renderable;

class XmlResponse extends RoutemasterResponse
{
    protected $renderable;

    /**
     * @param Renderable $renderable
     */
	public function __construct($renderable)
	{
		parent::__construct([]);
		$this->renderable = $renderable;
		$this->headers[] = 'Content-Type: application/xml';
	}

    protected function render()
    {
        $this->renderable->render();
    }
}