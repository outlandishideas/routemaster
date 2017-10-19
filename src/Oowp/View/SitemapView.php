<?php

namespace Outlandish\Wordpress\Routemaster\Oowp\View;

use Outlandish\Wordpress\Oowp\OowpQuery;
use Outlandish\Wordpress\Oowp\PostTypes\WordpressPost;

class SitemapView extends RoutemasterOowpView
{
    protected $pageItems;

    /**
     * @param OowpQuery|WordpressPost[] $pageItems
     */
    public function __construct($pageItems)
    {
        parent::__construct([]);
        $this->pageItems = $pageItems;
    }


    public function render($args = [])
    {
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        ?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
            <?php foreach ($this->pageItems as $post) : ?>
            <url>
                <?php $this->renderPost($post); ?>
            </url>
            <?php endforeach; ?>
        </urlset>
        <?php
    }

    /**
     * @param WordpressPost $post
     */
    protected function renderPost($post)
    {
        ?>
            <loc><?php echo $post->permalink(); ?></loc>
            <lastmod><?php echo $post->modifiedDate('Y-m-d'); ?></lastmod>
        <?php
    }
}