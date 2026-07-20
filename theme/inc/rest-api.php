<?php
// ABOUTME: REST-API-Endpoints für Kontaktformular und Mitmach-Einreichungen.
// ABOUTME: Prüft Nonce, Rate-Limit und hCaptcha vor jeder Datenbankoperation.

function wuerde_register_form_routes() {
    register_rest_route( 'wuerde/v1', '/kontakt', [
        'methods'             => 'POST',
        'callback'            => 'wuerde_handle_kontakt',
        'permission_callback' => '__return_true',
        'args'                => [
            'name'          => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
            'email'         => [
                'required'          => true,
                'sanitize_callback' => 'sanitize_email',
                'validate_callback' => 'is_email',
            ],
            'nachricht'     => [ 'required' => true,  'sanitize_callback' => 'sanitize_textarea_field' ],
            'captcha_token' => [ 'required' => false, 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ],
            'nonce'         => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
        ],
    ] );

    register_rest_route( 'wuerde/v1', '/einreichung', [
        'methods'             => 'POST',
        'callback'            => 'wuerde_handle_einreichung',
        'permission_callback' => '__return_true',
        'args'                => [
            'name'          => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
            'email'         => [
                'required'          => true,
                'sanitize_callback' => 'sanitize_email',
                'validate_callback' => 'is_email',
            ],
            'titel'         => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
            'beschreibung'  => [ 'required' => true,  'sanitize_callback' => 'sanitize_textarea_field' ],
            'kurzbeschreibung' => [ 'required' => false, 'sanitize_callback' => 'sanitize_textarea_field', 'default' => '' ],
            'telefon'       => [ 'required' => false, 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ],
            'kategorie_ids' => [
                'required'          => true,
                'sanitize_callback' => function ( $v ) { return array_map( 'absint', (array) $v ); },
                'validate_callback' => function ( $v ) {
                    $ids = array_filter( array_map( 'absint', (array) $v ) );
                    return ! empty( $ids );
                },
            ],
            'email_public'   => [ 'required' => false, 'sanitize_callback' => 'rest_sanitize_boolean', 'default' => false ],
            'telefon_public' => [ 'required' => false, 'sanitize_callback' => 'rest_sanitize_boolean', 'default' => false ],
            'ort'           => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
            'lat'           => [
                'required'          => false,
                'sanitize_callback' => function ( $v ) { return (float) $v; },
                'validate_callback' => function ( $v ) { return $v === '' || ( (float) $v >= -90 && (float) $v <= 90 ); },
                'default'           => 0,
            ],
            'lng'           => [
                'required'          => false,
                'sanitize_callback' => function ( $v ) { return (float) $v; },
                'validate_callback' => function ( $v ) { return $v === '' || ( (float) $v >= -180 && (float) $v <= 180 ); },
                'default'           => 0,
            ],
            'captcha_token' => [ 'required' => false, 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ],
            'nonce'         => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
        ],
    ] );
}
add_action( 'rest_api_init', 'wuerde_register_form_routes' );

// Nonce-Hilfsfunktionen für öffentliche Formulare (immer User 0, unabhängig vom eingeloggten Admin).
function wuerde_public_nonce( string $action ): string {
    $prev = get_current_user_id();
    wp_set_current_user( 0 );
    $nonce = wp_create_nonce( $action );
    wp_set_current_user( $prev );
    return $nonce;
}

function wuerde_verify_public_nonce( string $nonce, string $action ): bool {
    $prev  = get_current_user_id();
    wp_set_current_user( 0 );
    $valid = (bool) wp_verify_nonce( $nonce, $action );
    wp_set_current_user( $prev );
    return $valid;
}

// Gibt false zurück wenn das Limit für diese IP und Aktion bereits erreicht ist.
function wuerde_check_rate_limit( string $action ): bool {
    $ip  = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
    $key = 'wuerde_rate_' . $action . '_' . md5( $ip );
    if ( get_transient( $key ) ) {
        return false;
    }
    set_transient( $key, 1, HOUR_IN_SECONDS );
    return true;
}

function wuerde_handle_kontakt( WP_REST_Request $request ): WP_REST_Response {
    if ( ! wuerde_verify_public_nonce( $request->get_param( 'nonce' ), 'wuerde_kontakt' ) ) {
        return new WP_REST_Response( [ 'error' => 'Ungültige Anfrage.' ], 403 );
    }

    if ( ! wuerde_check_rate_limit( 'kontakt' ) ) {
        return new WP_REST_Response( [ 'error' => 'Bitte warte eine Stunde vor der nächsten Nachricht.' ], 429 );
    }

    if ( get_option( 'wuerde_hcaptcha_site_key', '' ) && ! wuerde_verify_hcaptcha( $request->get_param( 'captcha_token' ) ) ) {
        return new WP_REST_Response( [ 'error' => 'Captcha-Verifikation fehlgeschlagen.' ], 400 );
    }

    $name     = $request->get_param( 'name' );
    $email    = $request->get_param( 'email' );
    $nachricht = $request->get_param( 'nachricht' );
    $to       = wuerde_notification_email();
    $subject  = 'Neue Kontaktanfrage von ' . $name;
    $body     = "Name: {$name}\nE-Mail: {$email}\n\nNachricht:\n{$nachricht}";
    $headers  = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $email,
    ];

    $sent = wp_mail( $to, $subject, $body, $headers );

    if ( ! $sent ) {
        set_transient(
            'wuerde_mail_fehler',
            'Kontaktformular: E-Mail konnte nicht gesendet werden. Bitte SMTP-Konfiguration prüfen.',
            DAY_IN_SECONDS
        );
    }

    return new WP_REST_Response( [ 'success' => true ], 200 );
}

function wuerde_handle_einreichung( WP_REST_Request $request ): WP_REST_Response {
    if ( ! wuerde_verify_public_nonce( $request->get_param( 'nonce' ), 'wuerde_einreichung' ) ) {
        return new WP_REST_Response( [ 'error' => 'Ungültige Anfrage.' ], 403 );
    }

    if ( ! wuerde_check_rate_limit( 'einreichung' ) ) {
        return new WP_REST_Response( [ 'error' => 'Bitte warte eine Stunde vor der nächsten Einreichung.' ], 429 );
    }

    if ( get_option( 'wuerde_hcaptcha_site_key', '' ) && ! wuerde_verify_hcaptcha( $request->get_param( 'captcha_token' ) ) ) {
        return new WP_REST_Response( [ 'error' => 'Captcha-Verifikation fehlgeschlagen.' ], 400 );
    }

    $post_id = wp_insert_post( [
        'post_title'   => $request->get_param( 'titel' ),
        'post_content' => $request->get_param( 'beschreibung' ),
        'post_status'  => 'pending',
        'post_type'    => 'wuerde_beitrag',
    ], true );

    if ( is_wp_error( $post_id ) ) {
        return new WP_REST_Response( [ 'error' => 'Einreichung konnte nicht gespeichert werden.' ], 500 );
    }

    update_post_meta( $post_id, 'wuerde_einreichung_name',  $request->get_param( 'name' ) );
    update_post_meta( $post_id, 'wuerde_einreichung_email', $request->get_param( 'email' ) );

    $telefon = $request->get_param( 'telefon' );
    if ( $telefon ) {
        update_post_meta( $post_id, 'wuerde_einreichung_telefon', $telefon );
    }

    $kurzbeschreibung = $request->get_param( 'kurzbeschreibung' );
    if ( $kurzbeschreibung ) {
        update_post_meta( $post_id, 'wuerde_kurzbeschreibung', $kurzbeschreibung );
    }

    update_post_meta( $post_id, 'wuerde_einreichung_email_public',   (bool) $request->get_param( 'email_public' ) );
    update_post_meta( $post_id, 'wuerde_einreichung_telefon_public', $telefon && (bool) $request->get_param( 'telefon_public' ) );

    $lat = (float) $request->get_param( 'lat' );
    $lng = (float) $request->get_param( 'lng' );
    if ( $lat && $lng ) {
        update_post_meta( $post_id, 'wuerde_lat', $lat );
        update_post_meta( $post_id, 'wuerde_lng', $lng );
    }

    $kategorie_ids = array_filter( array_map( 'absint', (array) $request->get_param( 'kategorie_ids' ) ), function ( $id ) {
        return $id && term_exists( $id, 'wuerde_kategorie' );
    } );
    if ( ! empty( $kategorie_ids ) ) {
        wp_set_post_terms( $post_id, array_values( $kategorie_ids ), 'wuerde_kategorie' );
    }

    $ort = $request->get_param( 'ort' );
    if ( $ort ) {
        wp_set_post_terms( $post_id, [ $ort ], 'wuerde_ort' );
    }

    $name    = $request->get_param( 'name' );
    $email   = $request->get_param( 'email' );
    $titel   = $request->get_param( 'titel' );
    $to      = wuerde_notification_email();
    $subject = "Neue Einreichung: {$titel} (Referenz #{$post_id})";
    $body    = "Referenz-Nr.: #{$post_id}\nName: {$name}\nE-Mail: {$email}"
             . ( $telefon ? "\nTelefon: {$telefon}" : '' )
             . "\n\nTitel: {$titel}\n\nBeschreibung:\n" . $request->get_param( 'beschreibung' );
    $headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

    $sent = wp_mail( $to, $subject, $body, $headers );
    if ( ! $sent ) {
        set_transient(
            'wuerde_mail_fehler',
            "Neue Einreichung #{$post_id} gespeichert, aber Benachrichtigungs-E-Mail konnte nicht gesendet werden. Bitte SMTP-Konfiguration prüfen.",
            DAY_IN_SECONDS
        );
    }

    return new WP_REST_Response( [ 'success' => true, 'post_id' => $post_id ], 200 );
}
