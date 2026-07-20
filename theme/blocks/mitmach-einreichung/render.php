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
    'sectionBeitrag'              => 'Dein Beitrag',
    'sectionOrt'                  => 'Ort',
    'sectionKontakt'              => 'Deine Kontaktdaten',
    'labelName'                   => 'Name',
    'labelEmail'                  => 'E-Mail-Adresse',
    'labelTitel'                  => 'Titel',
    'labelBeschreibung'           => 'Beschreibung',
    'labelKategorie'              => 'Kategorie(n)',
    'labelKurzbeschreibung'       => 'Kurzbeschreibung des Angebots (freiwillig)',
    'labelTelefon'                => 'Telefonnummer (freiwillig)',
    'labelAdresse'                => 'Adresse',
    'labelOrt'                    => 'Ort (freiwillig)',
    'labelEmailPublic'            => 'Ich bin einverstanden, dass meine E-Mail-Adresse auf der Beitragsseite veröffentlicht wird.',
    'labelTelefonPublic'          => 'Ich bin einverstanden, dass meine Telefonnummer auf der Beitragsseite veröffentlicht wird.',
    'placeholderTitel'            => 'z. B. Kunstprojekt im Stadtmuseum',
    'placeholderBeschreibung'     => 'Was habt ihr gemacht? Was war besonders?',
    'placeholderKurzbeschreibung' => 'Max. zwei Sätze, z. B. für die Übersicht',
    'placeholderTelefon'          => 'z. B. 0151 23456789',
    'placeholderAdresse'          => 'z. B. Marktplatz 1, München',
    'placeholderOrt'              => 'z. B. München',
    'btnAdresse'                  => 'Suchen',
    'btnSubmit'                   => 'Beitrag einreichen',
    'karteToggle'                 => 'Ort auf Karte auswählen (optional)',
    'dateiHinweisVor'             => 'Fotos und Dokumente bitte per E-Mail an',
    'dateiHinweisNach'            => 'senden — nach dem Absenden erhältst du eine Referenznummer.',
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
        <fieldset class="wuerde-mitmach-einreichung__section">
            <legend class="wuerde-mitmach-einreichung__section-title"><?php echo esc_html( $attr['sectionBeitrag'] ); ?></legend>

            <div class="wuerde-mitmach-einreichung__field">
                <label for="wuerde-einr-titel"><?php echo esc_html( $attr['labelTitel'] ); ?> <span aria-hidden="true">*</span></label>
                <input type="text" id="wuerde-einr-titel" name="titel" required
                       placeholder="<?php echo esc_attr( $attr['placeholderTitel'] ); ?>">
            </div>
            <fieldset class="wuerde-mitmach-einreichung__field wuerde-mitmach-einreichung__kategorien">
                <legend><?php echo esc_html( $attr['labelKategorie'] ); ?> <span aria-hidden="true">*</span></legend>
                <?php foreach ( (array) $kategorien as $term ) : ?>
                <label class="wuerde-mitmach-einreichung__checkbox">
                    <input type="checkbox" name="kategorie_ids[]" value="<?php echo esc_attr( (string) $term->term_id ); ?>">
                    <?php echo esc_html( $term->name ); ?>
                </label>
                <?php endforeach; ?>
            </fieldset>
            <div class="wuerde-mitmach-einreichung__field">
                <label for="wuerde-einr-beschreibung"><?php echo esc_html( $attr['labelBeschreibung'] ); ?> <span aria-hidden="true">*</span></label>
                <textarea id="wuerde-einr-beschreibung" name="beschreibung" rows="8" required
                          placeholder="<?php echo esc_attr( $attr['placeholderBeschreibung'] ); ?>"></textarea>
            </div>
            <div class="wuerde-mitmach-einreichung__field">
                <label for="wuerde-einr-kurzbeschreibung"><?php echo esc_html( $attr['labelKurzbeschreibung'] ); ?></label>
                <textarea id="wuerde-einr-kurzbeschreibung" name="kurzbeschreibung" rows="2"
                          placeholder="<?php echo esc_attr( $attr['placeholderKurzbeschreibung'] ); ?>"></textarea>
            </div>
        </fieldset>

        <fieldset class="wuerde-mitmach-einreichung__section">
            <legend class="wuerde-mitmach-einreichung__section-title"><?php echo esc_html( $attr['sectionOrt'] ); ?></legend>

            <div class="wuerde-mitmach-einreichung__row">
                <div class="wuerde-mitmach-einreichung__field wuerde-mitmach-einreichung__field--adresse">
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
                <div class="wuerde-mitmach-einreichung__field wuerde-mitmach-einreichung__field--ort">
                    <label for="wuerde-einr-ort"><?php echo esc_html( $attr['labelOrt'] ); ?></label>
                    <input type="text" id="wuerde-einr-ort" name="ort" autocomplete="off"
                           placeholder="<?php echo esc_attr( $attr['placeholderOrt'] ); ?>">
                </div>
            </div>
            <details class="wuerde-mitmach-einreichung__karte-toggle">
                <summary><?php echo esc_html( $attr['karteToggle'] ); ?></summary>
                <div id="wuerde-einr-map"
                     style="height:320px;border-radius:4px;border:1px solid #ddd;margin-top:8px"></div>
            </details>
            <input type="hidden" name="lat" value="">
            <input type="hidden" name="lng" value="">
        </fieldset>

        <fieldset class="wuerde-mitmach-einreichung__section">
            <legend class="wuerde-mitmach-einreichung__section-title"><?php echo esc_html( $attr['sectionKontakt'] ); ?></legend>

            <div class="wuerde-mitmach-einreichung__field">
                <label for="wuerde-einr-name"><?php echo esc_html( $attr['labelName'] ); ?> <span aria-hidden="true">*</span></label>
                <input type="text" id="wuerde-einr-name" name="name" required autocomplete="name">
            </div>
            <div class="wuerde-mitmach-einreichung__field">
                <label for="wuerde-einr-email"><?php echo esc_html( $attr['labelEmail'] ); ?> <span aria-hidden="true">*</span></label>
                <input type="email" id="wuerde-einr-email" name="email" required autocomplete="email">
                <label class="wuerde-mitmach-einreichung__checkbox wuerde-mitmach-einreichung__checkbox--consent">
                    <input type="checkbox" id="wuerde-einr-email-public" name="email_public">
                    <?php echo esc_html( $attr['labelEmailPublic'] ); ?>
                </label>
            </div>
            <div class="wuerde-mitmach-einreichung__field">
                <label for="wuerde-einr-telefon"><?php echo esc_html( $attr['labelTelefon'] ); ?></label>
                <input type="tel" id="wuerde-einr-telefon" name="telefon" autocomplete="tel"
                       placeholder="<?php echo esc_attr( $attr['placeholderTelefon'] ); ?>">
                <label class="wuerde-mitmach-einreichung__checkbox wuerde-mitmach-einreichung__checkbox--consent">
                    <input type="checkbox" id="wuerde-einr-telefon-public" name="telefon_public">
                    <?php echo esc_html( $attr['labelTelefonPublic'] ); ?>
                </label>
            </div>
        </fieldset>

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
