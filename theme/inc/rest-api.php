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
            'kategorie_id'  => [ 'required' => false, 'sanitize_callback' => 'absint', 'default' => 0 ],
            'ort'           => [ 'required' => false, 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ],
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
    if ( ! wp_verify_nonce( $request->get_param( 'nonce' ), 'wuerde_kontakt' ) ) {
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
    if ( ! wp_verify_nonce( $request->get_param( 'nonce' ), 'wuerde_einreichung' ) ) {
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

    $lat = (float) $request->get_param( 'lat' );
    $lng = (float) $request->get_param( 'lng' );
    if ( $lat && $lng ) {
        update_post_meta( $post_id, 'wuerde_lat', $lat );
        update_post_meta( $post_id, 'wuerde_lng', $lng );
    }

    $kategorie_id = (int) $request->get_param( 'kategorie_id' );
    if ( $kategorie_id && term_exists( $kategorie_id, 'wuerde_kategorie' ) ) {
        wp_set_post_terms( $post_id, [ $kategorie_id ], 'wuerde_kategorie' );
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
    $body    = "Referenz-Nr.: #{$post_id}\nName: {$name}\nE-Mail: {$email}\n\nTitel: {$titel}\n\nBeschreibung:\n" . $request->get_param( 'beschreibung' );
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
