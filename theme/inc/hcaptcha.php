<?php
// ABOUTME: hCaptcha-Token-Verifikation via hcaptcha.com/siteverify API.
// ABOUTME: Fail-closed: ohne konfigurierten Secret Key schlägt die Verifikation fehl.

// Aktiv nur wenn beide Schlüssel gepflegt sind — sonst würde entweder das Widget
// nicht gerendert (kein Site Key) oder die Verifikation ins Leere laufen (kein Secret).
function wuerde_hcaptcha_enabled(): bool {
    return get_option( 'wuerde_hcaptcha_site_key', '' ) !== ''
        && get_option( 'wuerde_hcaptcha_secret_key', '' ) !== '';
}

function wuerde_verify_hcaptcha( string $token ): bool {
    $secret = get_option( 'wuerde_hcaptcha_secret_key', '' );
    if ( empty( $secret ) ) {
        return false;
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

// Warnung im Admin wenn nur einer der beiden hCaptcha-Schlüssel gepflegt ist.
function wuerde_hcaptcha_config_notice() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $site_key = get_option( 'wuerde_hcaptcha_site_key', '' );
    $secret   = get_option( 'wuerde_hcaptcha_secret_key', '' );
    if ( ( $site_key === '' ) === ( $secret === '' ) ) {
        return;
    }
    $url = admin_url( 'options-general.php?page=wuerde-formulare' );
    echo '<div class="notice notice-warning"><p>'
        . 'hCaptcha ist unvollständig konfiguriert (nur ein Schlüssel gesetzt) und daher <strong>deaktiviert</strong>. '
        . 'Bitte beide Schlüssel unter <a href="' . esc_url( $url ) . '">Einstellungen → Formulare</a> eintragen.'
        . '</p></div>';
}
add_action( 'admin_notices', 'wuerde_hcaptcha_config_notice' );
