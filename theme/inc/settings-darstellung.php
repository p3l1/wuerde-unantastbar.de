<?php
// ABOUTME: Admin-Seite „Darstellung" unter Mitmach-Beiträge zur Wahl der Header-Verlaufsvariante.
// ABOUTME: Die Vorschau-Miniaturen entstehen über dieselbe Funktion wie der Banner im Frontend.

function wuerde_darstellung_menu() {
    $hook = add_submenu_page(
        'edit.php?post_type=wuerde_beitrag',
        'Darstellung',
        'Darstellung',
        'manage_options',
        'wuerde-darstellung',
        'wuerde_darstellung_page_html'
    );

    if ( $hook ) {
        add_action( 'load-' . $hook, 'wuerde_darstellung_load' );
    }
}
add_action( 'admin_menu', 'wuerde_darstellung_menu' );

// Über load-{hook} statt eines fest getippten Hook-Suffix — der ändert sich mit dem Elternmenü.
function wuerde_darstellung_load() {
    add_action( 'admin_enqueue_scripts', 'wuerde_darstellung_admin_assets' );
}

function wuerde_darstellung_settings_init() {
    register_setting( 'wuerde_darstellung', 'wuerde_header_gradient_variant', [
        'type'              => 'string',
        'sanitize_callback' => 'wuerde_sanitize_header_gradient_variant',
        'default'           => wuerde_kategorie_gradient_default_variant(),
    ] );

    add_settings_section(
        'wuerde_header_gradient',
        'Beitrags-Header',
        'wuerde_header_gradient_section_html',
        'wuerde-darstellung'
    );

    add_settings_field(
        'wuerde_header_gradient_variant',
        'Verlaufs-Variante',
        'wuerde_field_header_gradient_variant',
        'wuerde-darstellung',
        'wuerde_header_gradient'
    );
}
add_action( 'admin_init', 'wuerde_darstellung_settings_init' );

function wuerde_sanitize_header_gradient_variant( $value ): string {
    $value = is_string( $value ) ? $value : '';

    return array_key_exists( $value, wuerde_kategorie_gradient_varianten() )
        ? $value
        : wuerde_kategorie_gradient_default_variant();
}

function wuerde_header_gradient_section_html() {
    echo '<p class="description">Beiträge ohne Beitragsbild bekommen einen farbigen Header aus den Farben '
        . '<strong>aller</strong> zugeordneten Kategorien. Hier wird festgelegt, wie diese Farben ineinander übergehen. '
        . 'Beiträge mit Beitragsbild und die Kategorieseiten bleiben unverändert.</p>';
}

function wuerde_field_header_gradient_variant() {
    $current = wuerde_kategorie_gradient_variant();
    $colors  = wuerde_darstellung_preview_colors();
    $single  = [ $colors[0] ];
    ?>
    <fieldset class="wuerde-gradient-choices">
        <legend class="screen-reader-text">Verlaufs-Variante für den Beitrags-Header</legend>

        <?php foreach ( wuerde_kategorie_gradient_varianten() as $slug => $variant ) :
            $is_current = $slug === $current;
            ?>
            <label class="wuerde-gradient-card<?php echo $is_current ? ' is-selected' : ''; ?>">
                <span class="wuerde-gradient-card__preview"
                      style="background-image: <?php echo esc_attr( wuerde_kategorie_gradient( $colors, $slug ) ); ?>;"
                      aria-hidden="true"></span>

                <span class="wuerde-gradient-card__head">
                    <input type="radio"
                           name="wuerde_header_gradient_variant"
                           value="<?php echo esc_attr( $slug ); ?>"
                           <?php checked( $is_current ); ?>>
                    <span class="wuerde-gradient-card__name"><?php echo esc_html( $variant['label'] ); ?></span>
                    <span class="wuerde-gradient-card__check" aria-hidden="true">Ausgewählt</span>
                </span>

                <span class="wuerde-gradient-card__desc"><?php echo esc_html( $variant['description'] ); ?></span>

                <span class="wuerde-gradient-card__single">
                    <span class="wuerde-gradient-card__single-preview"
                          style="background-image: <?php echo esc_attr( wuerde_kategorie_gradient( $single, $slug ) ); ?>;"
                          aria-hidden="true"></span>
                    <span class="wuerde-gradient-card__single-label">Beitrag mit nur einer Kategorie</span>
                </span>
            </label>
        <?php endforeach; ?>
    </fieldset>
    <?php
}

/**
 * Die ersten vier Kategoriefarben; ohne brauchbare Terms eine feste Beispielpalette.
 *
 * Term-Meta kann `var(--…)`-Tokens enthalten — die lösen sich im Admin nicht auf.
 *
 * @return string[] Mindestens ein Wert.
 */
function wuerde_darstellung_preview_colors(): array {
    $colors = [];
    $terms  = get_terms( [
        'taxonomy'   => 'wuerde_kategorie',
        'hide_empty' => false,
    ] );

    if ( ! is_wp_error( $terms ) ) {
        foreach ( $terms as $term ) {
            $color = trim( (string) get_term_meta( $term->term_id, 'wuerde_color_token', true ) );
            if ( '' !== $color && '#' === $color[0] ) {
                $colors[] = $color;
            }
        }
    }

    if ( count( $colors ) < 2 ) {
        $colors = [ '#00ACA0', '#F7BC2F', '#E41D21', '#96D3DF' ];
    }

    return array_slice( $colors, 0, 4 );
}

function wuerde_darstellung_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    if ( isset( $_GET['settings-updated'] ) ) {
        add_settings_error( 'wuerde_darstellung', 'wuerde_darstellung_saved', 'Einstellungen gespeichert.', 'success' );
    }
    ?>
    <div class="wrap">
        <h1>Darstellung</h1>
        <?php settings_errors( 'wuerde_darstellung' ); ?>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'wuerde_darstellung' );
            do_settings_sections( 'wuerde-darstellung' );
            submit_button( 'Einstellungen speichern' );
            ?>
        </form>
    </div>
    <?php
}

function wuerde_darstellung_admin_assets() {
    wp_register_style( 'wuerde-darstellung-admin', false, [], null );
    wp_enqueue_style( 'wuerde-darstellung-admin' );
    wp_add_inline_style( 'wuerde-darstellung-admin', wuerde_darstellung_admin_css() );
}

function wuerde_darstellung_admin_css(): string {
    return <<<CSS
.wuerde-gradient-choices {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin: 0;
    max-width: 960px;
}

.wuerde-gradient-card {
    position: relative;
    display: block;
    width: 288px;
    padding: 12px;
    border: 2px solid #dcdcde;
    border-radius: 6px;
    background: #fff;
    cursor: pointer;
}

.wuerde-gradient-card:hover {
    border-color: #8c8f94;
}

.wuerde-gradient-card:focus-within {
    outline: 2px solid #2271b1;
    outline-offset: 2px;
}

/* .is-selected kommt aus PHP; :has() hält die Markierung beim Klicken ohne Reload aktuell. */
.wuerde-gradient-card.is-selected,
.wuerde-gradient-card:has(input:checked) {
    border-color: #2271b1;
    box-shadow: 0 0 0 1px #2271b1;
}

.wuerde-gradient-card:has(input:not(:checked)) {
    border-color: #dcdcde;
    box-shadow: none;
}

.wuerde-gradient-card:has(input:not(:checked)):hover {
    border-color: #8c8f94;
}

.wuerde-gradient-card__preview {
    display: block;
    width: 100%;
    height: 90px;
    border-radius: 4px;
    background-color: #f0f0f1;
}

.wuerde-gradient-card__head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 12px 0 4px;
}

.wuerde-gradient-card__head input[type="radio"] {
    margin: 0;
    flex: none;
}

.wuerde-gradient-card__name {
    font-size: 14px;
    font-weight: 600;
}

.wuerde-gradient-card__check {
    display: none;
    margin-left: auto;
    padding: 2px 8px;
    border-radius: 999px;
    background: #2271b1;
    color: #fff;
    font-size: 11px;
    line-height: 1.6;
    white-space: nowrap;
}

.wuerde-gradient-card__check::before {
    content: "\\2713\\00a0";
}

.wuerde-gradient-card.is-selected .wuerde-gradient-card__check,
.wuerde-gradient-card:has(input:checked) .wuerde-gradient-card__check {
    display: inline-block;
}

.wuerde-gradient-card:has(input:not(:checked)) .wuerde-gradient-card__check {
    display: none;
}

.wuerde-gradient-card__desc {
    display: block;
    min-height: 3em;
    color: #50575e;
    font-size: 13px;
    line-height: 1.5;
}

.wuerde-gradient-card__single {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #f0f0f1;
}

.wuerde-gradient-card__single-preview {
    display: block;
    flex: none;
    width: 96px;
    height: 32px;
    border-radius: 3px;
    background-color: #f0f0f1;
}

.wuerde-gradient-card__single-label {
    color: #646970;
    font-size: 12px;
    line-height: 1.4;
}
CSS;
}
