<?php
// ABOUTME: Custom Post Type für Vereinsmitglieder der "Über uns"-Seite.
// ABOUTME: Speichert Name, Rolle, Jahrgang, Kurzbiografie und verknüpft das Portrait als Featured Image.

function wuerde_register_person_cpt() {
    register_post_type( 'wuerde_person', [
        'labels'       => [
            'name'               => 'Personen',
            'singular_name'      => 'Person',
            'add_new_item'       => 'Neue Person hinzufügen',
            'edit_item'          => 'Person bearbeiten',
            'search_items'       => 'Personen suchen',
            'not_found'          => 'Keine Personen gefunden.',
            'not_found_in_trash' => 'Keine Personen im Papierkorb.',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => true,
        'supports'     => [ 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes' ],
        'menu_icon'    => 'dashicons-groups',
        'rewrite'      => false,
        'has_archive'  => false,
    ] );
}
add_action( 'init', 'wuerde_register_person_cpt' );

function wuerde_register_person_meta() {
    $args = [
        'object_subtype' => 'wuerde_person',
        'type'           => 'string',
        'single'         => true,
        'show_in_rest'   => true,
        'auth_callback'  => function() { return current_user_can( 'edit_posts' ); },
    ];
    register_meta( 'post', 'person_role',      $args );
    register_meta( 'post', 'person_birthyear', $args );
    register_meta( 'post', 'person_button_url', $args );
}
add_action( 'init', 'wuerde_register_person_meta' );

function wuerde_person_meta_box() {
    add_meta_box(
        'wuerde_person_fields',
        'Personen-Details',
        'wuerde_person_meta_box_html',
        'wuerde_person',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wuerde_person_meta_box' );

function wuerde_person_meta_box_html( WP_Post $post ) {
    wp_nonce_field( 'wuerde_person_meta', 'wuerde_person_nonce' );
    $fields = [
        'person_role'       => [ 'label' => 'Rolle / Berufe',  'type' => 'text', 'placeholder' => 'z.B. Tischler · Theologe · Diakon' ],
        'person_birthyear'  => [ 'label' => 'Jahrgang',        'type' => 'text', 'placeholder' => 'z.B. 1964' ],
        'person_button_url' => [ 'label' => 'Button-URL',       'type' => 'url',  'placeholder' => 'https://' ],
    ];
    echo '<table class="form-table" role="presentation"><tbody>';
    foreach ( $fields as $key => $field ) {
        $value = esc_attr( get_post_meta( $post->ID, $key, true ) );
        $ph    = esc_attr( $field['placeholder'] );
        echo "<tr><th scope='row'><label for='{$key}'>{$field['label']}</label></th>";
        echo "<td><input type='{$field['type']}' id='{$key}' name='{$key}' value='{$value}' placeholder='{$ph}' class='regular-text'></td></tr>";
    }
    echo '</tbody></table>';
    echo '<p class="description">Kurzbiografie: im Textbereich oben eingeben. Portrait: als Beitragsbild (rechte Sidebar) hochladen.</p>';
}

function wuerde_save_person_meta( int $post_id ) {
    if ( ! isset( $_POST['wuerde_person_nonce'] ) ) return;
    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wuerde_person_nonce'] ) ), 'wuerde_person_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $keys = [ 'person_role', 'person_birthyear', 'person_button_url' ];
    foreach ( $keys as $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
        }
    }
}
add_action( 'save_post_wuerde_person', 'wuerde_save_person_meta' );
