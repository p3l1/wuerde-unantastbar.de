<?php
// ABOUTME: Registriert Custom Blocks und den REST-Endpoint für Kartenmarker.
// ABOUTME: Fügt die Block-Kategorie "Würde unantastbar" im Editor hinzu.

function wuerde_register_blocks() {
    foreach ( [ 'mitmach-list', 'mitmach-map', 'team-grid', 'grundidee-banner', 'impressionen-teaser' ] as $slug ) {
        register_block_type( get_template_directory() . '/blocks/' . $slug );
    }

    wp_register_style(
        'leaflet',
        get_template_directory_uri() . '/assets/leaflet/leaflet.css',
        [],
        '1.9.4'
    );

    wp_register_script(
        'leaflet',
        get_template_directory_uri() . '/assets/leaflet/leaflet.js',
        [],
        '1.9.4',
        true
    );
}
add_action( 'init', 'wuerde_register_blocks' );

function wuerde_register_block_category( array $categories ) {
    return array_merge(
        [
            [
                'slug'  => 'wuerde',
                'title' => 'Würde unantastbar',
                'icon'  => 'heart',
            ],
        ],
        $categories
    );
}
add_filter( 'block_categories_all', 'wuerde_register_block_category' );

function wuerde_register_rest_routes() {
    register_rest_route( 'wuerde/v1', '/map-points', [
        'methods'             => 'GET',
        'callback'            => 'wuerde_map_points_handler',
        'permission_callback' => '__return_true',
    ] );
}
add_action( 'rest_api_init', 'wuerde_register_rest_routes' );

function wuerde_map_points_handler( WP_REST_Request $request ): WP_REST_Response {
    $kategorie = sanitize_key( $request->get_param( 'kategorie' ) );

    $query_args = [
        'post_type'      => 'wuerde_beitrag',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ];

    if ( $kategorie ) {
        $query_args['tax_query'] = [ [
            'taxonomy' => 'wuerde_kategorie',
            'field'    => 'slug',
            'terms'    => $kategorie,
        ] ];
    }

    $posts = get_posts( $query_args );

    $points = [];
    foreach ( $posts as $post ) {
        $lat = (float) get_post_meta( $post->ID, 'wuerde_lat', true );
        $lng = (float) get_post_meta( $post->ID, 'wuerde_lng', true );

        if ( ! $lat || ! $lng ) {
            continue;
        }

        $terms = wp_get_post_terms( $post->ID, 'wuerde_kategorie', [ 'fields' => 'slugs' ] );

        $points[] = [
            'id'            => $post->ID,
            'title'         => $post->post_title,
            'lat'           => $lat,
            'lng'           => $lng,
            'category_slug' => ! empty( $terms ) ? $terms[0] : '',
            'permalink'     => get_permalink( $post->ID ),
        ];
    }

    return new WP_REST_Response( $points, 200 );
}
