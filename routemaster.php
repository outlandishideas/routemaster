<?php
/*
Plugin Name: Routemaster
Description: An implementation of the MVC pattern where WordPress provides the model.
Version: 1.1
*/

require_once 'Routemaster.class.php';
require_once 'RoutemasterView.class.php';
require_once 'RoutemasterException.class.php';

//include ooPost compatibility class if oowp plugin is active
add_action('plugins_loaded', function(){
	if (class_exists('ooPost')) {
		require_once 'ooRoutemaster.class.php';

        //change preview link to have routing that can be picked up by Routemaster
        add_filter('preview_post_link', function ($link) {
            $qs = parse_url($link, PHP_URL_QUERY);
            $args = wp_parse_args($qs);

            //only modify link of unpublished posts (published posts use preview_id param)
            if (isset($args['p']) || isset($args['page_id'])) {
                $id = isset($args['p']) ? $args['p'] : $args['page_id'];
                $post = ulPost::createPostObject($id);
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

//clean up from earlier version of plugin
if (file_exists(ABSPATH . 'index-rm.php')) {
	add_action('init', function(){
		remove_action('mod_rewrite_rules', 'rm_mod_rewrite_rules');
		require_once(ABSPATH . 'wp-admin/includes/admin.php');
		flush_rewrite_rules(true);
		unlink(ABSPATH . 'index-rm.php');
		if (!is_admin()) {
			wp_redirect(get_bloginfo('url'));
			exit;
		}
	});
}