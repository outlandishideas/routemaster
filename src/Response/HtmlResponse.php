<?php

namespace Outlandish\Wordpress\Routemaster\Response;

use Outlandish\Wordpress\Routemaster\View\Renderable;

class HtmlResponse extends RoutemasterResponse
{
    protected $view;

    /**
     * @param Renderable $view
     */
    public function __construct($view)
    {
        parent::__construct([]);
        $this->view = $view;
    }

    protected function render()
    {
        $this->view->render();
    }

}