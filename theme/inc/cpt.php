<?php
// ABOUTME: Custom Post Type, Taxonomien, Post Meta und Admin-Oberfläche für Mitmach-Beiträge.
// ABOUTME: Registriert wuerde_beitrag mit wuerde_kategorie/wuerde_ort und Koordinaten-Meta-Box.

function wuerde_register_cpt() {
    register_post_type( 'wuerde_beitrag', [
        'labels'        => [
            'name'               => 'Mitmach-Beiträge',
            'singular_name'      => 'Mitmach-Beitrag',
            'add_new_item'       => 'Neuen Beitrag hinzufügen',
            'edit_item'          => 'Beitrag bearbeiten',
            'search_items'       => 'Beiträge suchen',
            'not_found'          => 'Keine Beiträge gefunden.',
            'not_found_in_trash' => 'Keine Beiträge im Papierkorb.',
        ],
        'public'        => true,
        'has_archive'   => false,
        'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-heart',
        'rewrite'       => [ 'slug' => 'beitrag' ],
    ] );

    register_taxonomy( 'wuerde_kategorie', 'wuerde_beitrag', [
        'labels'            => [
            'name'          => 'Kategorien',
            'singular_name' => 'Kategorie',
            'edit_item'     => 'Kategorie bearbeiten',
            'add_new_item'  => 'Neue Kategorie hinzufügen',
        ],
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'mitmach-kategorie' ],
    ] );

    register_taxonomy( 'wuerde_ort', 'wuerde_beitrag', [
        'labels'            => [
            'name'          => 'Orte',
            'singular_name' => 'Ort',
            'edit_item'     => 'Ort bearbeiten',
            'add_new_item'  => 'Neuen Ort hinzufügen',
        ],
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'mitmach-ort' ],
    ] );

    register_post_meta( 'wuerde_beitrag', 'wuerde_lat', [
        'type'         => 'number',
        'single'       => true,
        'show_in_rest' => true,
    ] );

    register_post_meta( 'wuerde_beitrag', 'wuerde_lng', [
        'type'         => 'number',
        'single'       => true,
        'show_in_rest' => true,
    ] );
}
add_action( 'init', 'wuerde_register_cpt' );

// Initiale Kategorie-Terms anlegen und veraltete Terms entfernen (idempotent).
function wuerde_seed_categories() {
    $obsolete = [
        'kirchengemeinden',
        'kommunal',
        'handwerk',
        'gesundheit',
        'sonstiges',
    ];

    foreach ( $obsolete as $slug ) {
        $term = get_term_by( 'slug', $slug, 'wuerde_kategorie' );
        if ( $term ) {
            wp_delete_term( $term->term_id, 'wuerde_kategorie' );
        }
    }

    $terms = [
        'kunst-kultur'     => 'Kunst und Kultur',
        'gespraech'        => 'Gespräch und Diskussion',
        'strasse'          => 'Auf der Straße',
        'spiel-spass'      => 'Spiel und Spaß',
        'bildung'          => 'Bildungseinrichtungen',
        'soziales'         => 'Soziale Einrichtungen',
        'betriebe'         => 'Betriebe und Unternehmen',
    ];

    foreach ( $terms as $slug => $name ) {
        if ( ! term_exists( $slug, 'wuerde_kategorie' ) ) {
            wp_insert_term( $name, 'wuerde_kategorie', [ 'slug' => $slug ] );
        }
    }
}
add_action( 'init', 'wuerde_seed_categories' );

// Meta-Box für Koordinaten im Beitrags-Editor.
function wuerde_register_koordinaten_meta_box() {
    add_meta_box(
        'wuerde_koordinaten',
        'Kartenposition (Koordinaten)',
        'wuerde_render_koordinaten_meta_box',
        'wuerde_beitrag',
        'side'
    );
}
add_action( 'add_meta_boxes', 'wuerde_register_koordinaten_meta_box' );

function wuerde_render_koordinaten_meta_box( WP_Post $post ) {
    wp_nonce_field( 'wuerde_koordinaten_save', 'wuerde_koordinaten_nonce' );

    $lat = get_post_meta( $post->ID, 'wuerde_lat', true );
    $lng = get_post_meta( $post->ID, 'wuerde_lng', true );
    ?>
    <p>
        <label for="wuerde_lat"><strong>Breitengrad (lat)</strong></label><br>
        <input type="number" id="wuerde_lat" name="wuerde_lat"
               value="<?php echo esc_attr( $lat ); ?>"
               step="0.000001" min="-90" max="90" style="width:100%">
    </p>
    <p>
        <label for="wuerde_lng"><strong>Längengrad (lng)</strong></label><br>
        <input type="number" id="wuerde_lng" name="wuerde_lng"
               value="<?php echo esc_attr( $lng ); ?>"
               step="0.000001" min="-180" max="180" style="width:100%">
    </p>
    <p style="color:#666;font-size:12px">Dezimalgrad, z. B. 48.137154 / 11.576124 für München.</p>
    <?php
}

function wuerde_save_koordinaten_meta( int $post_id ) {
    if (
        ! isset( $_POST['wuerde_koordinaten_nonce'] ) ||
        ! wp_verify_nonce( sanitize_key( $_POST['wuerde_koordinaten_nonce'] ), 'wuerde_koordinaten_save' )
    ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['wuerde_lat'] ) ) {
        update_post_meta( $post_id, 'wuerde_lat', (float) $_POST['wuerde_lat'] );
    }

    if ( isset( $_POST['wuerde_lng'] ) ) {
        update_post_meta( $post_id, 'wuerde_lng', (float) $_POST['wuerde_lng'] );
    }
}
add_action( 'save_post_wuerde_beitrag', 'wuerde_save_koordinaten_meta' );

// Term-Meta für Kategorie-Farbe (CSS-Variable).
function wuerde_register_kategorie_color_meta() {
    register_term_meta( 'wuerde_kategorie', 'wuerde_color_token', [
        'type'         => 'string',
        'single'       => true,
        'show_in_rest' => false,
    ] );
}
add_action( 'init', 'wuerde_register_kategorie_color_meta' );

// Term-Meta-Feld im Kategorie-Bearbeiten-Formular.
function wuerde_kategorie_color_field( WP_Term $term ) {
    $color = get_term_meta( $term->term_id, 'wuerde_color_token', true );
    ?>
    <tr class="form-field">
        <th scope="row"><label for="wuerde_color_token">Farbe (CSS-Variable)</label></th>
        <td>
            <input type="text" id="wuerde_color_token" name="wuerde_color_token"
                   value="<?php echo esc_attr( $color ); ?>"
                   placeholder="z. B. var(--color-cat-kirche)">
            <p class="description">CSS-Variable aus style.css, z. B. <code>var(--color-cat-kirche)</code>.</p>
        </td>
    </tr>
    <?php
}
add_action( 'wuerde_kategorie_edit_form_fields', 'wuerde_kategorie_color_field' );

function wuerde_save_kategorie_color( int $term_id ) {
    if ( isset( $_POST['wuerde_color_token'] ) ) {
        update_term_meta( $term_id, 'wuerde_color_token', sanitize_text_field( $_POST['wuerde_color_token'] ) );
    }
}
add_action( 'edited_wuerde_kategorie', 'wuerde_save_kategorie_color' );
