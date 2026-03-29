<?php
// ABOUTME: Theme setup and asset registration.
// ABOUTME: Add theme supports, register menus, and enqueue styles/scripts here.

function wuerde_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ] );
}
add_action( 'after_setup_theme', 'wuerde_setup' );

function wuerde_enqueue_assets() {
    wp_enqueue_style( 'wuerde-style', get_stylesheet_uri(), [], wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'wuerde_enqueue_assets' );
