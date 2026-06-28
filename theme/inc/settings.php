<?php
// ABOUTME: Admin-Einstellungsseite für Kontaktformular-Konfiguration.
// ABOUTME: Verwaltet hCaptcha-Schlüssel und Benachrichtigungs-E-Mail via WP Options API.

function wuerde_settings_menu() {
    add_options_page(
        'Formular-Einstellungen',
        'Formulare',
        'manage_options',
        'wuerde-formulare',
        'wuerde_settings_page_html'
    );
}
add_action( 'admin_menu', 'wuerde_settings_menu' );

function wuerde_settings_init() {
    register_setting( 'wuerde_formulare', 'wuerde_hcaptcha_site_key',   [ 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ] );
    register_setting( 'wuerde_formulare', 'wuerde_hcaptcha_secret_key', [ 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ] );
    register_setting( 'wuerde_formulare', 'wuerde_notification_email',  [ 'type' => 'string', 'sanitize_callback' => 'sanitize_email' ] );
    register_setting( 'wuerde_formulare', 'wuerde_machmit_url',         [ 'type' => 'string', 'sanitize_callback' => 'esc_url_raw' ] );

    add_settings_section( 'wuerde_hcaptcha', 'hCaptcha', '__return_false', 'wuerde-formulare' );
    add_settings_section( 'wuerde_email',    'E-Mail-Benachrichtigung', '__return_false', 'wuerde-formulare' );
    add_settings_section( 'wuerde_nav',      'Navigation', '__return_false', 'wuerde-formulare' );

    add_settings_field( 'wuerde_hcaptcha_site_key',   'Site Key',   'wuerde_field_site_key',   'wuerde-formulare', 'wuerde_hcaptcha' );
    add_settings_field( 'wuerde_hcaptcha_secret_key', 'Secret Key', 'wuerde_field_secret_key', 'wuerde-formulare', 'wuerde_hcaptcha' );
    add_settings_field( 'wuerde_notification_email',  'Benachrichtigungs-E-Mail', 'wuerde_field_notify_email', 'wuerde-formulare', 'wuerde_email' );
    add_settings_field( 'wuerde_machmit_url',         'Mach-mit-Seite', 'wuerde_field_machmit_url', 'wuerde-formulare', 'wuerde_nav' );
}
add_action( 'admin_init', 'wuerde_settings_init' );

function wuerde_field_site_key() {
    $val = get_option( 'wuerde_hcaptcha_site_key', '' );
    echo '<input type="text" name="wuerde_hcaptcha_site_key" value="' . esc_attr( $val ) . '" class="regular-text">';
    echo '<p class="description">Von hcaptcha.com → Meine Website → Site Key</p>';
}

function wuerde_field_secret_key() {
    $val = get_option( 'wuerde_hcaptcha_secret_key', '' );
    echo '<input type="password" name="wuerde_hcaptcha_secret_key" value="' . esc_attr( $val ) . '" class="regular-text">';
    echo '<p class="description">Von hcaptcha.com → Meine Website → Secret Key</p>';
}

function wuerde_field_notify_email() {
    $val = get_option( 'wuerde_notification_email', '' );
    echo '<input type="email" name="wuerde_notification_email" value="' . esc_attr( $val ) . '" class="regular-text">';
    echo '<p class="description">Leer lassen für WordPress-Standard-Admin-E-Mail (' . esc_html( get_option( 'admin_email' ) ) . ')</p>';
}

function wuerde_settings_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Formular-Einstellungen</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'wuerde_formulare' );
            do_settings_sections( 'wuerde-formulare' );
            submit_button( 'Einstellungen speichern' );
            ?>
        </form>
    </div>
    <?php
}

function wuerde_field_machmit_url() {
    $val = get_option( 'wuerde_machmit_url', '' );
    echo '<input type="url" name="wuerde_machmit_url" value="' . esc_attr( $val ) . '" class="regular-text" placeholder="https://">';
    echo '<p class="description">URL der „Mach mit"-Seite — wird für alle Zurück-Buttons auf Beitrags- und Archivseiten verwendet.</p>';
}

function wuerde_machmit_url(): string {
    $url = get_option( 'wuerde_machmit_url', '' );
    return ! empty( $url ) ? $url : ( get_post_type_archive_link( 'wuerde_beitrag' ) ?: get_home_url( null, '/mach-mit/' ) );
}

function wuerde_notification_email(): string {
    $email = get_option( 'wuerde_notification_email', '' );
    return ! empty( $email ) ? $email : (string) get_option( 'admin_email' );
}
