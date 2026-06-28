<?php
// ABOUTME: Server-side Rendering des Mitmach-Einreichungs-Blocks.
// ABOUTME: Lädt Kategorien, Leaflet und bettet Konfiguration als data-Attribute ein.

$site_key     = get_option( 'wuerde_hcaptcha_site_key', '' );
$notify_email = wuerde_notification_email();

$kategorien = get_terms( [
    'taxonomy'   => 'wuerde_kategorie',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
] );

if ( $site_key ) {
    wp_enqueue_script( 'hcaptcha', 'https://js.hcaptcha.com/1/api.js', [], null, false );
}

wp_enqueue_style( 'leaflet' );
wp_enqueue_script( 'leaflet' );

$attr = wp_parse_args( $attributes, [
    'labelName'               => 'Name',
    'labelEmail'              => 'E-Mail-Adresse',
    'labelTitel'              => 'Titel',
    'labelBeschreibung'       => 'Beschreibung',
    'labelKategorie'          => 'Kategorie',
    'labelAdresse'            => 'Adresse',
    'labelOrt'                => 'Ort',
    'placeholderTitel'        => 'z. B. Kunstprojekt im Stadtmuseum',
    'placeholderBeschreibung' => 'Was habt ihr gemacht? Was war besonders?',
    'placeholderAdresse'      => 'z. B. Marktplatz 1, München',
    'placeholderOrt'          => 'z. B. München',
    'placeholderKategorie'    => '— Bitte wählen (optional) —',
    'btnAdresse'              => 'Suchen',
    'btnSubmit'               => 'Beitrag einreichen',
    'karteToggle'             => 'Ort auf Karte auswählen (optional)',
    'dateiHinweisVor'         => 'Fotos und Dokumente bitte per E-Mail an',
    'dateiHinweisNach'        => 'senden — nach dem Absenden erhältst du eine Referenznummer.',
] );
?>
<div class="wuerde-mitmach-einreichung wp-block-wuerde-mitmach-einreichung">
    <form
        class="wuerde-mitmach-einreichung__form"
        data-endpoint="<?php echo esc_url( rest_url( 'wuerde/v1/einreichung' ) ); ?>"
        data-nonce="<?php echo esc_attr( wuerde_public_nonce( 'wuerde_einreichung' ) ); ?>"
        data-notify-email="<?php echo esc_attr( $notify_email ); ?>"
        data-crown="<?php echo esc_url( get_template_directory_uri() . '/assets/krone-white.png' ); ?>"
        novalidate
    >
        <div class="wuerde-mitmach-einreichung__field">
            <label for="wuerde-einr-name"><?php echo esc_html( $attr['labelName'] ); ?> <span aria-hidden="true">*</span></label>
            <input type="text" id="wuerde-einr-name" name="name" required autocomplete="name">
        </div>
        <div class="wuerde-mitmach-einreichung__field">
            <label for="wuerde-einr-email"><?php echo esc_html( $attr['labelEmail'] ); ?> <span aria-hidden="true">*</span></label>
            <input type="email" id="wuerde-einr-email" name="email" required autocomplete="email">
        </div>
        <div class="wuerde-mitmach-einreichung__field">
            <label for="wuerde-einr-titel"><?php echo esc_html( $attr['labelTitel'] ); ?> <span aria-hidden="true">*</span></label>
            <input type="text" id="wuerde-einr-titel" name="titel" required
                   placeholder="<?php echo esc_attr( $attr['placeholderTitel'] ); ?>">
        </div>
        <div class="wuerde-mitmach-einreichung__field">
            <label for="wuerde-einr-beschreibung"><?php echo esc_html( $attr['labelBeschreibung'] ); ?> <span aria-hidden="true">*</span></label>
            <textarea id="wuerde-einr-beschreibung" name="beschreibung" rows="8" required
                      placeholder="<?php echo esc_attr( $attr['placeholderBeschreibung'] ); ?>"></textarea>
        </div>
        <div class="wuerde-mitmach-einreichung__field">
            <label for="wuerde-einr-kategorie"><?php echo esc_html( $attr['labelKategorie'] ); ?></label>
            <select id="wuerde-einr-kategorie" name="kategorie_id">
                <option value=""><?php echo esc_html( $attr['placeholderKategorie'] ); ?></option>
                <?php foreach ( (array) $kategorien as $term ) : ?>
                <option value="<?php echo esc_attr( (string) $term->term_id ); ?>">
                    <?php echo esc_html( $term->name ); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="wuerde-mitmach-einreichung__field">
            <label for="wuerde-einr-adresse"><?php echo esc_html( $attr['labelAdresse'] ); ?></label>
            <div class="wuerde-mitmach-einreichung__adresse-row">
                <input type="text" id="wuerde-einr-adresse" autocomplete="off"
                       placeholder="<?php echo esc_attr( $attr['placeholderAdresse'] ); ?>">
                <button type="button" class="btn btn--primary wuerde-mitmach-einreichung__adresse-btn">
                    <?php echo esc_html( $attr['btnAdresse'] ); ?>
                </button>
            </div>
            <span class="wuerde-mitmach-einreichung__adresse-hint" aria-live="polite"></span>
        </div>
        <div class="wuerde-mitmach-einreichung__field">
            <label for="wuerde-einr-ort"><?php echo esc_html( $attr['labelOrt'] ); ?></label>
            <input type="text" id="wuerde-einr-ort" name="ort" autocomplete="off"
                   placeholder="<?php echo esc_attr( $attr['placeholderOrt'] ); ?>">
        </div>
        <details class="wuerde-mitmach-einreichung__karte-toggle">
            <summary><?php echo esc_html( $attr['karteToggle'] ); ?></summary>
            <div id="wuerde-einr-map"
                 style="height:320px;border-radius:4px;border:1px solid #ddd;margin-top:8px"></div>
        </details>
        <input type="hidden" name="lat" value="">
        <input type="hidden" name="lng" value="">
        <?php if ( $site_key ) : ?>
        <div class="h-captcha" data-sitekey="<?php echo esc_attr( $site_key ); ?>"></div>
        <?php endif; ?>
        <p class="wuerde-mitmach-einreichung__datei-hinweis">
            <?php echo esc_html( $attr['dateiHinweisVor'] ); ?>
            <a href="mailto:<?php echo esc_attr( $notify_email ); ?>"><?php echo esc_html( $notify_email ); ?></a>
            <?php echo esc_html( $attr['dateiHinweisNach'] ); ?>
        </p>
        <button type="submit" class="btn btn--primary"><?php echo esc_html( $attr['btnSubmit'] ); ?></button>
        <div class="wuerde-mitmach-einreichung__status" aria-live="polite" hidden></div>
    </form>
    <div class="wuerde-mitmach-einreichung__erfolg" hidden></div>
</div>
