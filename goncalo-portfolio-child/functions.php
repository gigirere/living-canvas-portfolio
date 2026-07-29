<?php
/**
 * Gonçalo Gonçalves — Portfolio 2026 (Child theme).
 *
 * The parent theme does all the work. This file only loads the child's
 * style.css after the parent's stylesheet, so your overrides win.
 *
 * Add your own PHP snippets below instead of editing the parent theme.
 *
 * @package goncalo-portfolio-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the child stylesheet after the parent's.
 *
 * Block themes do not enqueue style.css automatically, so do it explicitly.
 */
function gp_child_enqueue_styles() {
	$child_css = get_stylesheet_directory() . '/style.css';

	wp_enqueue_style(
		'gp-portfolio-child',
		get_stylesheet_uri(),
		array( 'gp-portfolio' ), // the parent's handle — load after it
		file_exists( $child_css ) ? (string) filemtime( $child_css ) : false
	);
}
add_action( 'wp_enqueue_scripts', 'gp_child_enqueue_styles', 20 );

/**
 * Load the child theme's own translations, if you add any.
 */
function gp_child_setup() {
	load_child_theme_textdomain( 'goncalo-portfolio-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'gp_child_setup' );
