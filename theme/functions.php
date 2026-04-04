<?php
// ABOUTME: Theme setup and asset registration.
// ABOUTME: Add theme supports, register menus, and enqueue styles/scripts here.

function wuerde_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ] );

    // Block Editor: Nutze theme.json für Farben & Typografie (kein add_theme_support nötig)
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );

    // Navigation
    register_nav_menus( [
        'primary' => __( 'Hauptnavigation', 'wuerde-unantastbar' ),
        'footer'  => __( 'Footer-Navigation', 'wuerde-unantastbar' ),
    ] );
}
add_action( 'after_setup_theme', 'wuerde_setup' );

function wuerde_enqueue_assets() {
    wp_enqueue_style(
        'wuerde-style',
        get_stylesheet_uri(),
        [],
        wp_get_theme()->get( 'Version' )
    );

    // Lookbook-Assets nur auf der Lookbook-Seite laden
    if ( is_page_template( 'page-lookbook.php' ) ) {
        wp_enqueue_style(
            'wuerde-lookbook',
            get_stylesheet_directory_uri() . '/lookbook.css',
            [ 'wuerde-style' ],
            wp_get_theme()->get( 'Version' )
        );

        wp_enqueue_script(
            'wuerde-lookbook',
            get_stylesheet_directory_uri() . '/lookbook.js',
            [],
            wp_get_theme()->get( 'Version' ),
            [ 'strategy' => 'defer', 'in_footer' => true ]
        );
    }
}
add_action( 'wp_enqueue_scripts', 'wuerde_enqueue_assets' );
