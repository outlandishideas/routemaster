<?php

namespace Outlandish\Wordpress\Routemaster;

use Outlandish\Wordpress\Routemaster\Response\HtmlResponse;
use Outlandish\Wordpress\Routemaster\View\HtmlPage;
use Outlandish\Wordpress\Routemaster\View\Renderable;

class RouterHelper
{
    /**
     * Routes use this when creating a response, if a routing function doesn't explicitly return a response
     * @param array|object $args
     * @return HtmlResponse
     */
    public function createDefaultResponse($args = [])
    {
        if (!($args instanceof Renderable)) {
            $args = new HtmlPage(the_title(), the_content());
        }
        return new HtmlResponse($args);
    }

    public function createNotFoundResponse()
	{
		return new HtmlResponse(new HtmlPage('Not found', ''));
	}

    public function getRequestMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function isPost() {
        return $this->getRequestMethod() == 'POST';
    }

    public function isGet() {
        return $this->getRequestMethod() == 'GET';
    }

    public function isPut() {
        return $this->getRequestMethod() == 'PUT';
    }

    public function isDelete() {
        return $this->getRequestMethod() == 'DELETE';
    }

    public function getRequestBody() {
        $body = file_get_contents('php://input');
        return $body ? json_decode($body) : null;
    }
}