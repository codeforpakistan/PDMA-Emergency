<?php 

/** ==============================================================
	ADD JAVASCRIPT & CSS
	============================================================== **/

function pdma_enqueue_scripts(){
	$assets = get_template_directory_uri() . '/assets';
	wp_enqueue_style('bootstrap', $assets. '/bootstrap/css/bootstrap.min.css', array(), '3.3.6', 'all');
	wp_enqueue_style('pdma', $assets. '/pdma/pdma.css', array(), '0.0.1', 'all');
	wp_enqueue_script('bootstrap-js', $assets . '/bootstrap/js/bootstrap.min.js', array('jquery'), '3.3.6', true);
	wp_enqueue_script('pdma-js', $assets . '/pdma/pdma.js', array(), '0.0.1', true);
} // pdma_enqueue_scripts

add_action('wp_enqueue_scripts','pdma_enqueue_scripts');

/** ==============================================================
	ADD THEME SUPPORT
	============================================================== **/

function pdma_theme_setup(){
	add_theme_support('menus');
	add_theme_support( 'post-formats', array( 'aside', 'gallery' ) );
	add_theme_support( 'post-thumbnails' );
	register_nav_menu('top-nav', 'Top Navigation Menu');
	register_nav_menu('primary', 'Primary Navigation Menu');
} // pdma_theme_setup

add_action('init','pdma_theme_setup'); 

/** ==============================================================
	INCLUDE WALKER CLASS
	============================================================== **/

require get_template_directory() . '/inc/walker.php';

/** ==============================================================
	REMOVE VERSION
	============================================================== **/

function pdma_remove_version(){
	return '';
} // pdma_remove_version

add_filter('the_generator', 'pdma_remove_version');