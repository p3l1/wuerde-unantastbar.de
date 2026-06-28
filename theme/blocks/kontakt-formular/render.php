<?php
// ABOUTME: Server-side Rendering des Kontaktformular-Blocks.
// ABOUTME: Bettet Nonce und REST-Endpoint-URL als data-Attribute ein.

$site_key = get_option( 'wuerde_hcaptcha_site_key', '' );

if ( $site_key ) {
    wp_enqueue_script( 'hcaptcha', 'https://js.hcaptcha.com/1/api.js', [], null, false );
}
?>
<div class="wuerde-kontakt-formular wp-block-wuerde-kontakt-formular">
    <form
        class="wuerde-kontakt-formular__form"
        data-endpoint="<?php echo esc_url( rest_url( 'wuerde/v1/kontakt' ) ); ?>"
        data-nonce="<?php echo esc_attr( wp_create_nonce( 'wuerde_kontakt' ) ); ?>"
        novalidate
    >
        <div class="wuerde-kontakt-formular__field">
            <label for="wuerde-kontakt-name">Name <span aria-hidden="true">*</span></label>
            <input type="text" id="wuerde-kontakt-name" name="name" required autocomplete="name">
        </div>
        <div class="wuerde-kontakt-formular__field">
            <label for="wuerde-kontakt-email">E-Mail-Adresse <span aria-hidden="true">*</span></label>
            <input type="email" id="wuerde-kontakt-email" name="email" required autocomplete="email">
        </div>
        <div class="wuerde-kontakt-formular__field">
            <label for="wuerde-kontakt-nachricht">Nachricht <span aria-hidden="true">*</span></label>
            <textarea id="wuerde-kontakt-nachricht" name="nachricht" rows="6" required></textarea>
        </div>
        <?php if ( $site_key ) : ?>
        <div class="h-captcha" data-sitekey="<?php echo esc_attr( $site_key ); ?>"></div>
        <?php endif; ?>
        <button type="submit" class="btn btn--primary">Nachricht senden</button>
        <div class="wuerde-kontakt-formular__status" aria-live="polite" hidden></div>
    </form>
</div>
