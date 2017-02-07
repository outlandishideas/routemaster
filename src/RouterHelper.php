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