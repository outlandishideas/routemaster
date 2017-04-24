<?php
/*
Plugin Name: Routemaster
Description: An implementation of the MVC pattern where WordPress provides the model.
Version: 2.0.3
*/

//include ooPost compatibility class if oowp plugin is active
add_action('plugins_loaded', function(){
	if (class_exists('Outlandish\\Wordpress\\Oowp\\PostTypes\\WordpressPost')) {
        //change preview link to have routing that can be picked up by Routemaster
        add_filter('preview_post_link', function ($link) {
            $qs = parse_url($link, PHP_URL_QUERY);
            $args = wp_parse_args($qs);

            //only modify link of unpublished posts (published posts use preview_id param)
            if (isset($args['p']) || isset($args['page_id'])) {
                $id = isset($args['p']) ? $args['p'] : $args['page_id'];
                $post = Outlandish\Wordpress\Oowp\PostTypes\WordpressPost::createWordpressPost($id);
                if ($post) {
                    $post->post_name = sanitize_title($post->post_name ? $post->post_name : $post->post_title, $post->ID);
                    $link = $post->permalink();
                    $link .= '?' . $qs;
                }
            }

            return $link;
        });
	}
});

