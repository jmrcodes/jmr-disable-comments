<?php
/*
 * Plugin Name: Disable Comments
 * Plugin URI: https://github.com/jmrcodes/jmr-disable-comments
 * Description: Disables comments on sites that don't require the functionality
 * Version: 0.1.1
 * Author: James Robinson
 * Author URI: https://jmr.codes/
 * License: GPL3
 */

if ( !defined( 'ABSPATH' ) ) exit;

require 'plugin-update-checker/plugin-update-checker.php';

$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/jmrcodes/jmr-disable-comments',
    __FILE__,
    'jmr-disable-comments'
);


// Disable support for comments and trackbacks in post types
function jmr_disable_comments_post_type() {
	$post_types = get_post_types();
	foreach ($post_types as $post_type) {
		if ( post_type_supports($post_type, 'comments') ) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}
}
add_action('admin_init', 'jmr_disable_comments_post_type');


// Close comments on the front-end
function jmr_disable_comments_status() {
	return false;
}
add_filter('comments_open', 'jmr_disable_comments_status', 20, 2);
add_filter('pings_open', 'jmr_disable_comments_status', 20, 2);


// Hide existing comments
function jmr_disable_comments_existing($comments) {
	$comments = array();
	return $comments;
}
add_filter('comments_array', 'jmr_disable_comments_existing', 10, 2);


// Remove comments page in menu
function jmr_disable_comments_menu() {
	remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'jmr_disable_comments_menu');


// Redirect any user trying to access comments page
function jmr_disable_comments_menu_redirect() {
	global $pagenow;
	if ($pagenow === 'edit-comments.php') {
		wp_redirect(admin_url()); exit;
	}
}
add_action('admin_init', 'jmr_disable_comments_menu_redirect');


// Remove comments metabox from dashboard
function jmr_disable_comments_dashboard() {
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'jmr_disable_comments_dashboard');


// Remove comments from the admin toolbar
function jmr_disable_comments_toolbar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'jmr_disable_comments_toolbar' );
