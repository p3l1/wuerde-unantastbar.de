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

/**
 * Gibt die absolute URL zu einer Theme-Asset-Datei zurück.
 *
 * @param string $file Dateiname relativ zu theme/assets/ (z. B. 'krone.svg').
 * @return string Vollständige URL.
 */
function wuerde_asset_url( $file ) {
    return get_stylesheet_directory_uri() . '/assets/' . ltrim( $file, '/' );
}

function wuerde_enqueue_assets() {
    wp_enqueue_style(
        'wuerde-style',
        get_stylesheet_uri(),
        [],
        wp_get_theme()->get( 'Version' )
    );

    // Lookbook-Assets nur auf der Lookbook-Seite laden
    if ( is_page_template( 'page-lookbook.php' ) || is_page_template( 'page-hero-demo.php' ) ) {
        wp_enqueue_style(
            'wuerde-lookbook',
            get_stylesheet_directory_uri() . '/lookbook.css',
            [ 'wuerde-style' ],
            wp_get_theme()->get( 'Version' )
        );

        // Krone-Asset-URLs als CSS Custom Properties injizieren
        $crown_assets = [
            'white'  => wuerde_asset_url( 'krone-white.png' ),
            'teal'   => wuerde_asset_url( 'krone-teal.png' ),
            'yellow' => wuerde_asset_url( 'krone-yellow.png' ),
            'black'  => wuerde_asset_url( 'krone-black.png' ),
            'svg'    => wuerde_asset_url( 'krone.svg' ),
        ];
        $inline_css = ":root {\n";
        foreach ( $crown_assets as $key => $url ) {
            $inline_css .= "  --crown-url-{$key}: url('" . esc_url( $url ) . "');\n";
        }
        $inline_css .= '}';
        wp_add_inline_style( 'wuerde-lookbook', $inline_css );

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
