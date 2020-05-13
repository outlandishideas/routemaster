<?php

namespace Outlandish\Wordpress\Routemaster\View;

class HtmlPage implements Renderable
{
    protected $title, $content;

    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function render($args = [])
    {
        ?><!DOCTYPE html>
        <html lang="en" xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta charset="utf-8" />
            <title>My Site :: <?php echo $this->title; ?></title>

            <?php wp_head(); ?>
        </head>
        <body>

        <h1><?php echo $this->title; ?></h1>
        <div>
            <?php echo $this->content; ?>
        </div>

        </body>
        </html>
        <?php
    }
}
