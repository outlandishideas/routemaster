<?php

namespace Outlandish\Wordpress\Routemaster;

interface RoutemasterViewInterface
{
    /**
     * Renders the content to be displayed
     *
     * @param string $viewFile   the path to the view file that will be used to render this content
     * @param string $layoutFile the path to the layout file that will be used to render this content
     * @param array $variables   a key/valud array that will be made available to the template
     * @return void
     */
    public function render($viewFile, $layoutFile, $variables = array());
}