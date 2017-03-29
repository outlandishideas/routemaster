<?php
/*
Plugin Name: Routemaster
Description: An implementation of the MVC pattern where WordPress provides the model.
Version: 2.0.1
*/

define('ROUTEMASTER_NAMESPACE', 'Outlandish\\Wordpress\\Routemaster\\');

// add autoloader for routemaster classes
spl_autoload_register(function($class) {
    if (strpos($class, ROUTEMASTER_NAMESPACE) === 0) {
        $file = str_replace(ROUTEMASTER_NAMESPACE, '', $class);
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $file . '.php';
        if (file_exists($path)) {
            include($path);
            return true;
        }
    }
    return false;
});

//include ooPost compatibility class if oowp plugin is active
add_action('plugins_loaded', function(){
	if (class_exists('ooPost')) {
        //change preview link to have routing that can be picked up by Routemaster
        add_filter('preview_post_link', function ($link) {
            $qs = parse_url($link, PHP_URL_QUERY);
            $args = wp_parse_args($qs);

            //only modify link of unpublished posts (published posts use preview_id param)
            if (isset($args['p']) || isset($args['page_id'])) {
                $id = isset($args['p']) ? $args['p'] : $args['page_id'];
                $post = ooPost::createPostObject($id);
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

