<?php

namespace Outlandish\Wordpress\Routemaster\Response;

abstract class RoutemasterResponse
{
    abstract function render($args);
}