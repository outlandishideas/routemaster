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