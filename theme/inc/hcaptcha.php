<?php
// ABOUTME: hCaptcha-Token-Verifikation via hcaptcha.com/siteverify API.
// ABOUTME: Gibt true zurück wenn kein Secret Key konfiguriert (graceful degradation).

function wuerde_verify_hcaptcha( string $token ): bool {
    $secret = get_option( 'wuerde_hcaptcha_secret_key', '' );
    if ( empty( $secret ) ) {
        return true;
    }

    $response = wp_remote_post( 'https://hcaptcha.com/siteverify', [
        'body'    => [ 'secret' => $secret, 'response' => $token ],
        'timeout' => 10,
    ] );

    if ( is_wp_error( $response ) ) {
        return false;
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    return ! empty( $data['success'] );
}
