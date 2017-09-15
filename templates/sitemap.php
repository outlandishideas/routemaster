<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($this->pageItems as $post) : ?>
        <?php /** @var $post Outlandish\Wordpress\Oowp\PostTypes\WordpressPost */ ?>
        <url>
            <loc><?php echo $post->permalink(); ?></loc>
            <lastmod><?php echo $post->modifiedDate('Y-m-d'); ?></lastmod>
        </url>
    <?php endforeach; ?>
</urlset>
