<?php
/*
Plugin Name: Routemaster
Description: An implementation of the MVC pattern where WordPress provides the model.
*/

require_once 'Routemaster.class.php';
require_once 'ooRoutemaster.class.php';
require_once 'RoutemasterException.class.php';

register_activation_hook(__FILE__, function() {
	global $wp_rewrite;
	//rewrite .htaccess (and copy index file)
	$wp_rewrite->flush_rules(true);
});

register_deactivation_hook(__FILE__, function() {
	global $wp_rewrite;
	remove_filter('mod_rewrite_rules', 'rm_mod_rewrite_rules');
	//rewrite .htaccess
	$wp_rewrite->flush_rules(true);
	//delete alternate index file
	unlink(ABSPATH . 'index-rm.php');
});

//rewrite htaccess file to route via index-rm.php
add_filter('mod_rewrite_rules', 'rm_mod_rewrite_rules');
function rm_mod_rewrite_rules($rules) {
	//rewrite rewrite rules
	$rules = "DirectoryIndex index-rm.php index.php\n" . str_replace('index', 'index-rm', $rules);
//	$rules = preg_replace('%([\w/-]*)index.php \[L\]%', "$1index-rm.php [L]\nRewriteRule ^$ $1index-rm.php", $rules);

	//copy alternate index file
	if (!file_exists(ABSPATH . 'index-rm.php')) {
		copy(dirname(__FILE__) . '/index-rm.php', ABSPATH . 'index-rm.php');
	}

	return $rules;
}