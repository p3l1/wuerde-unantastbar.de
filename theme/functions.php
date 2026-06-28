<?php
// ABOUTME: Theme setup and asset registration.
// ABOUTME: Add theme supports, register menus, and enqueue styles/scripts here.

require_once get_template_directory() . '/inc/cpt.php';
require_once get_template_directory() . '/inc/cpt-person.php';
require_once get_template_directory() . '/inc/blocks.php';

function wuerde_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ] );

    // Block Editor: Nutze theme.json für Farben & Typografie (kein add_theme_support nötig)
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );

    // Fonts und Custom Properties im Block-Editor laden
    add_editor_style( 'style.css' );

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

    // Krone-Asset-URLs als CSS Custom Properties injizieren (immer geladen)
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
    wp_add_inline_style( 'wuerde-style', $inline_css );

    wp_enqueue_script(
        'wuerde-site',
        get_stylesheet_directory_uri() . '/site.js',
        [],
        wp_get_theme()->get( 'Version' ),
        [ 'strategy' => 'defer', 'in_footer' => true ]
    );
}
add_action( 'wp_enqueue_scripts', 'wuerde_enqueue_assets' );

/**
 * Lookbook-JS auf Template-Seiten laden.
 * Verwendet template_include — zu diesem Zeitpunkt ist der Template-Pfad bekannt.
 *
 * @param string $template Absoluter Pfad zum aktuellen Template.
 * @return string Unveränderter Template-Pfad.
 */
function wuerde_enqueue_lookbook_js( $template ) {
    $basename = basename( $template );
    if ( in_array( $basename, [ 'page-lookbook.php', 'page-hero-demo.php' ], true ) ) {
        wp_enqueue_script(
            'wuerde-lookbook',
            get_stylesheet_directory_uri() . '/lookbook.js',
            [],
            wp_get_theme()->get( 'Version' ),
            [ 'strategy' => 'defer', 'in_footer' => true ]
        );
    }
    return $template;
}
add_filter( 'template_include', 'wuerde_enqueue_lookbook_js' );

function wuerde_body_classes( $classes ) {
    if ( is_page_template( 'page-hero.php' ) ) {
        $classes[] = 'has-hero-template';
    }
    return $classes;
}
add_filter( 'body_class', 'wuerde_body_classes' );

function wuerde_hero_meta_box() {
    add_meta_box(
        'wuerde_hero_fields',
        'Hero-Einstellungen',
        'wuerde_hero_meta_box_html',
        'page',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wuerde_hero_meta_box' );

function wuerde_hero_meta_box_html( $post ) {
    if ( get_page_template_slug( $post->ID ) !== 'page-hero.php' ) {
        echo '<p style="color:#666;font-style:italic">Nur verfügbar wenn das Seitentemplate „Hero" ausgewählt ist.</p>';
        return;
    }
    wp_nonce_field( 'wuerde_hero_meta', 'wuerde_hero_nonce' );
    $fields = [
        'hero_title'       => [ 'label' => 'Titel',            'type' => 'text',  'placeholder' => get_bloginfo( 'name' ) ],
        'hero_subtitle'    => [ 'label' => 'Untertitel',       'type' => 'text',  'placeholder' => get_bloginfo( 'description' ) ],
'hero_button_text' => [ 'label' => 'Button-Text',      'type' => 'text',  'placeholder' => '' ],
        'hero_button_url'  => [ 'label' => 'Button-URL',       'type' => 'url',   'placeholder' => 'https://' ],
    ];
    echo '<table class="form-table" role="presentation"><tbody>';
    foreach ( $fields as $key => $field ) {
        $value = esc_attr( get_post_meta( $post->ID, $key, true ) );
        $ph    = esc_attr( $field['placeholder'] );
        echo "<tr><th scope='row'><label for='{$key}'>{$field['label']}</label></th>";
        echo "<td><input type='{$field['type']}' id='{$key}' name='{$key}' value='{$value}' placeholder='{$ph}' class='regular-text'></td></tr>";
    }
    echo '</tbody></table>';
}

function wuerde_save_hero_meta( $post_id ) {
    if ( ! isset( $_POST['wuerde_hero_nonce'] ) ) return;
    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wuerde_hero_nonce'] ) ), 'wuerde_hero_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $keys = [ 'hero_title', 'hero_subtitle', 'hero_button_text', 'hero_button_url' ];
    foreach ( $keys as $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
        }
    }
}
add_action( 'save_post', 'wuerde_save_hero_meta' );

function wuerde_register_hero_meta() {
    $args = [
        'object_subtype' => 'page',
        'type'           => 'string',
        'single'         => true,
        'show_in_rest'   => true,
        'auth_callback'  => function() { return current_user_can( 'edit_posts' ); },
    ];
    register_meta( 'post', 'hero_title',       $args );
    register_meta( 'post', 'hero_subtitle',    $args );
register_meta( 'post', 'hero_button_text', $args );
    register_meta( 'post', 'hero_button_url',  $args );
}
add_action( 'init', 'wuerde_register_hero_meta' );

function wuerde_banner_meta_box() {
    add_meta_box(
        'wuerde_banner_fields',
        'Banner-Einstellungen',
        'wuerde_banner_meta_box_html',
        'page',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wuerde_banner_meta_box' );

function wuerde_banner_meta_box_html( $post ) {
    if ( get_page_template_slug( $post->ID ) !== '' ) {
        echo '<p style="color:#666;font-style:italic">Nur verfügbar beim Standard-Seitentemplate.</p>';
        return;
    }
    wp_nonce_field( 'wuerde_banner_meta', 'wuerde_banner_nonce' );

    $height = get_post_meta( $post->ID, 'banner_height',    true ) ?: 'md';
    $pos    = get_post_meta( $post->ID, 'banner_image_pos', true ) ?: 'center 40%';

    $height_options = [
        'sm' => 'Klein (280 px)',
        'md' => 'Mittel (Standard)',
        'lg' => 'Groß',
    ];
    $pos_options = [
        'center top'    => 'Oben',
        'center 20%'    => 'Oberes Drittel',
        'center 40%'    => 'Mitte-Oben (Standard)',
        'center center' => 'Mitte',
        'center 60%'    => 'Mitte-Unten',
        'center bottom' => 'Unten',
    ];

    echo '<table class="form-table" role="presentation"><tbody>';

    echo '<tr><th scope="row"><label for="banner_height">Höhe</label></th><td><select id="banner_height" name="banner_height">';
    foreach ( $height_options as $val => $label ) {
        printf( '<option value="%s"%s>%s</option>', esc_attr( $val ), selected( $height, $val, false ), esc_html( $label ) );
    }
    echo '</select></td></tr>';

    echo '<tr><th scope="row"><label for="banner_image_pos">Bildausschnitt</label></th><td><select id="banner_image_pos" name="banner_image_pos">';
    foreach ( $pos_options as $val => $label ) {
        printf( '<option value="%s"%s>%s</option>', esc_attr( $val ), selected( $pos, $val, false ), esc_html( $label ) );
    }
    echo '</select></td></tr>';

    echo '</tbody></table>';
}

function wuerde_save_banner_meta( $post_id ) {
    if ( ! isset( $_POST['wuerde_banner_nonce'] ) ) return;
    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wuerde_banner_nonce'] ) ), 'wuerde_banner_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $allowed_heights = [ 'sm', 'md', 'lg' ];
    $allowed_pos     = [ 'center top', 'center 20%', 'center 40%', 'center center', 'center 60%', 'center bottom' ];

    if ( isset( $_POST['banner_height'] ) ) {
        $val = sanitize_text_field( wp_unslash( $_POST['banner_height'] ) );
        if ( in_array( $val, $allowed_heights, true ) ) {
            update_post_meta( $post_id, 'banner_height', $val );
        }
    }
    if ( isset( $_POST['banner_image_pos'] ) ) {
        $val = sanitize_text_field( wp_unslash( $_POST['banner_image_pos'] ) );
        if ( in_array( $val, $allowed_pos, true ) ) {
            update_post_meta( $post_id, 'banner_image_pos', $val );
        }
    }
}
add_action( 'save_post', 'wuerde_save_banner_meta' );

function wuerde_register_banner_meta() {
    $args = [
        'object_subtype' => 'page',
        'type'           => 'string',
        'single'         => true,
        'show_in_rest'   => true,
        'auth_callback'  => function() { return current_user_can( 'edit_posts' ); },
    ];
    register_meta( 'post', 'banner_height',    $args );
    register_meta( 'post', 'banner_image_pos', $args );
}
add_action( 'init', 'wuerde_register_banner_meta' );

// Leaflet und Karten-Script auf Kategorie-Archivseiten laden.
function wuerde_enqueue_kategorie_archive_scripts() {
    if ( ! is_tax( 'wuerde_kategorie' ) ) {
        return;
    }
    wp_enqueue_style( 'leaflet' );
    wp_enqueue_script( 'leaflet' );
    wp_enqueue_script(
        'wuerde-mitmach-map-view',
        get_template_directory_uri() . '/blocks/mitmach-map/view.js',
        [ 'leaflet' ],
        '1.0.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'wuerde_enqueue_kategorie_archive_scripts' );

// wp_enqueue_media für Kategorie-Admin-Seiten laden (Term-Bild-Picker).
function wuerde_enqueue_term_admin_media() {
    $screen = get_current_screen();
    if ( $screen && 'wuerde_kategorie' === $screen->taxonomy ) {
        wp_enqueue_media();
    }
}
add_action( 'admin_enqueue_scripts', 'wuerde_enqueue_term_admin_media' );

// Leaflet im Admin für den Koordinaten-Picker laden.
function wuerde_enqueue_admin_leaflet() {
    $screen = get_current_screen();
    if ( ! $screen || $screen->post_type !== 'wuerde_beitrag' ) {
        return;
    }
    wp_enqueue_style( 'leaflet' );
    wp_enqueue_script( 'leaflet' );
}
add_action( 'admin_enqueue_scripts', 'wuerde_enqueue_admin_leaflet' );
