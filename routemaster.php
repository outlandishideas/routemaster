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
		flush_rewrite_rules(true);
		unlink(ABSPATH . 'index-rm.php');
	});
}