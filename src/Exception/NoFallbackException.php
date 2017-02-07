<?php

namespace Outlandish\Wordpress\Routemaster\Exception;

class NoFallbackException extends RoutemasterException {
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->allowFallback = false;
    }
}