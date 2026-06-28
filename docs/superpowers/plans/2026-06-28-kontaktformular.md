# Kontaktformular & Mitmach-Einreichung — Implementierungsplan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Zwei Gutenberg-Blöcke — Kontaktformular und Mitmach-Einreichungsformular — ohne externe Plugins, mit REST-API-Backend, hCaptcha-Schutz und Admin-Freigabe-Workflow.

**Architecture:** Zwei neue Blöcke (`kontakt-formular`, `mitmach-einreichung`) senden per `fetch()` an eigene WP REST API Endpoints (`/wp-json/wuerde/v1/kontakt` und `/wp-json/wuerde/v1/einreichung`). Eingereichte Beiträge landen als `wuerde_beitrag`-Posts mit Status `pending` im Backend. Fünf neue `inc/`-Dateien ergänzen das Theme; `functions.php`, `inc/cpt.php` und `inc/blocks.php` erhalten minimale Ergänzungen.

**Tech Stack:** WordPress REST API, PHP 8.x, Vanilla JS, Leaflet (bereits registriert), hCaptcha JS API, wp_mail()

## Global Constraints

- Alle ABOUTME-Kommentare zwingend (zwei Zeilen, `// ABOUTME: ` Präfix) — ohne Ausnahme
- Kein HTML-Output mit `echo` ohne passendes Escaping (`esc_html`, `esc_attr`, `esc_url`)
- Kein direkter `$_POST`/`$_SERVER`-Zugriff ohne Sanitisierung
- Funktion `wuerde_register_rest_routes` existiert bereits in `inc/blocks.php` — NIEMALS diesen Namen verwenden; neue Funktion heißt `wuerde_register_form_routes`
- Leaflet ist in `inc/blocks.php` bereits registriert (aber noch nicht enqueued); in render.php via `wp_enqueue_style('leaflet')` / `wp_enqueue_script('leaflet')` anfordern
- Kein `wp_localize_script()` — Konfiguration via `data-*`-Attribute auf dem Form-Element
- Alle Strings im UI: Deutsch

---

## Dateistruktur

### Neue Dateien

| Datei | Zweck |
|-------|-------|
| `theme/inc/settings.php` | Admin-Einstellungsseite (hCaptcha-Keys, Benachrichtigungs-E-Mail) |
| `theme/inc/hcaptcha.php` | `wuerde_verify_hcaptcha(string $token): bool` |
| `theme/inc/submissions-admin.php` | Warteliste-Untermenü, Dashboard-Widget, Mail-Fehler-Notice |
| `theme/inc/rest-api.php` | REST-Endpoints `/kontakt` und `/einreichung` |
| `theme/blocks/kontakt-formular/block.json` | Block-Manifest |
| `theme/blocks/kontakt-formular/editor.asset.php` | Editor-Script-Dependencies |
| `theme/blocks/kontakt-formular/editor.js` | Editor-Registrierung (statischer Placeholder) |
| `theme/blocks/kontakt-formular/render.php` | Server-side HTML inkl. Nonce + Endpoint-URL |
| `theme/blocks/kontakt-formular/view.js` | Frontend-Submit-Handler |
| `theme/blocks/kontakt-formular/view.css` | Formular-Styles |
| `theme/blocks/mitmach-einreichung/block.json` | Block-Manifest |
| `theme/blocks/mitmach-einreichung/editor.asset.php` | Editor-Script-Dependencies |
| `theme/blocks/mitmach-einreichung/editor.js` | Editor-Registrierung (statischer Placeholder) |
| `theme/blocks/mitmach-einreichung/render.php` | Server-side HTML mit Kategorie-Dropdown und Karte |
| `theme/blocks/mitmach-einreichung/view.js` | Submit-Handler + Leaflet-Karte + Erfolgsanzeige |
| `theme/blocks/mitmach-einreichung/view.css` | Formular- und Karten-Styles |

### Geänderte Dateien

| Datei | Änderung |
|-------|----------|
| `theme/functions.php` | 4 × `require_once` für neue inc-Dateien |
| `theme/inc/cpt.php` | 2 `register_post_meta` + 1 Meta-Box (Einsender-Daten) |
| `theme/inc/blocks.php` | 2 neue Block-Slugs in `wuerde_register_blocks()` |

---

## Task 1: Einstellungsseite + hCaptcha-Helper

**Files:**
- Create: `theme/inc/settings.php`
- Create: `theme/inc/hcaptcha.php`
- Modify: `theme/functions.php`

**Interfaces:**
- Produces:
  - `get_option('wuerde_hcaptcha_site_key'): string`
  - `get_option('wuerde_hcaptcha_secret_key'): string`
  - `get_option('wuerde_notification_email'): string`
  - `wuerde_verify_hcaptcha(string $token): bool`
  - `wuerde_notification_email(): string` (Fallback auf admin_email)

- [ ] **Schritt 1: Verifikation dass keine Einstellungsseite existiert**

  Öffne WP-Admin → Einstellungen. Eintrag "Formulare" darf noch nicht erscheinen.

- [ ] **Schritt 2: `theme/inc/settings.php` anlegen**

```php
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

    add_settings_section( 'wuerde_hcaptcha', 'hCaptcha', '__return_false', 'wuerde-formulare' );
    add_settings_section( 'wuerde_email',    'E-Mail-Benachrichtigung', '__return_false', 'wuerde-formulare' );

    add_settings_field( 'wuerde_hcaptcha_site_key',   'Site Key',   'wuerde_field_site_key',   'wuerde-formulare', 'wuerde_hcaptcha' );
    add_settings_field( 'wuerde_hcaptcha_secret_key', 'Secret Key', 'wuerde_field_secret_key', 'wuerde-formulare', 'wuerde_hcaptcha' );
    add_settings_field( 'wuerde_notification_email',  'Benachrichtigungs-E-Mail', 'wuerde_field_notify_email', 'wuerde-formulare', 'wuerde_email' );
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

function wuerde_notification_email(): string {
    $email = get_option( 'wuerde_notification_email', '' );
    return ! empty( $email ) ? $email : (string) get_option( 'admin_email' );
}
```

- [ ] **Schritt 3: `theme/inc/hcaptcha.php` anlegen**

```php
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
```

- [ ] **Schritt 4: `theme/functions.php` erweitern**

  Direkt nach den bestehenden `require_once`-Zeilen (nach `blocks.php`) einfügen:

```php
require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/hcaptcha.php';
require_once get_template_directory() . '/inc/submissions-admin.php';
require_once get_template_directory() . '/inc/rest-api.php';
```

  Hinweis: `submissions-admin.php` und `rest-api.php` existieren noch nicht — das ist erwartet. PHP gibt keinen Fehler solange die Dateien nicht vorhanden sind... Warte, das stimmt nicht. `require_once` gibt einen Fatal Error wenn die Datei nicht existiert. Deshalb: alle vier Zeilen erst einfügen wenn alle Dateien angelegt sind (nach Task 4).

  **Jetzt nur diese zwei einfügen:**

```php
require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/hcaptcha.php';
```

- [ ] **Schritt 5: Einstellungsseite im Admin prüfen**

  WordPress-Admin öffnen → Einstellungen → "Formulare" muss erscheinen. Drei Felder sichtbar: Site Key, Secret Key, Benachrichtigungs-E-Mail. Einen Testwert eintragen und speichern. Seite neu laden — Wert muss erhalten bleiben.

- [ ] **Schritt 6: hCaptcha-Helper manuell prüfen**

  WP-Admin → Werkzeuge → (oder temporär in einer `wp-login.php`-freien Datei):

  ```php
  // Kurzer Smoke-Test in einer temporären WP-Admin-Seite oder per WP-CLI:
  // wp eval "var_dump(wuerde_verify_hcaptcha(''));"
  // Erwartet: bool(true)  — weil kein Secret Key konfiguriert
  ```

  Alternativ: Secret Key in den Einstellungen auf einen Dummy-Wert setzen, dann:
  ```
  // wp eval "var_dump(wuerde_verify_hcaptcha('invalid-token'));"
  // Erwartet: bool(false)
  ```

- [ ] **Schritt 7: Commit**

```bash
git add theme/inc/settings.php theme/inc/hcaptcha.php theme/functions.php
git commit -S -m "feat(formulare): Einstellungsseite und hCaptcha-Helper"
```

---

## Task 2: CPT-Erweiterungen (Einsender-Meta)

**Files:**
- Modify: `theme/inc/cpt.php`

**Interfaces:**
- Consumes: nothing new
- Produces:
  - Post-Meta `wuerde_einreichung_name` (string, nicht öffentlich)
  - Post-Meta `wuerde_einreichung_email` (string, nicht öffentlich)
  - Meta-Box "Einsender" im `wuerde_beitrag`-Editor (sidebar, high priority)

- [ ] **Schritt 1: Prüfen dass Einsender-Meta-Box noch nicht existiert**

  WP-Admin → Mitmach-Beiträge → beliebigen Beitrag öffnen. Rechte Sidebar: keine "Einsender"-Box vorhanden.

- [ ] **Schritt 2: Post-Meta-Registrierung und Meta-Box zu `theme/inc/cpt.php` hinzufügen**

  Am Ende der Datei (nach `add_action( 'edited_wuerde_kategorie', 'wuerde_save_kategorie_image' );`) einfügen:

```php
// Einsender-Metadaten für öffentliche Einreichungen.
function wuerde_register_einreichung_meta() {
    $args = [
        'type'         => 'string',
        'single'       => true,
        'show_in_rest' => false,
    ];
    register_post_meta( 'wuerde_beitrag', 'wuerde_einreichung_name',  $args );
    register_post_meta( 'wuerde_beitrag', 'wuerde_einreichung_email', $args );
}
add_action( 'init', 'wuerde_register_einreichung_meta' );

function wuerde_einreichung_meta_box() {
    add_meta_box(
        'wuerde_einreichung',
        'Einsender',
        'wuerde_einreichung_meta_box_html',
        'wuerde_beitrag',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wuerde_einreichung_meta_box' );

function wuerde_einreichung_meta_box_html( WP_Post $post ) {
    $name  = get_post_meta( $post->ID, 'wuerde_einreichung_name',  true );
    $email = get_post_meta( $post->ID, 'wuerde_einreichung_email', true );

    echo '<table style="border-collapse:collapse;width:100%"><tbody>';
    echo '<tr><th style="text-align:left;padding:3px 0;width:80px;font-weight:600">Ref.-Nr.</th>';
    echo '<td style="padding:3px 0"><strong>#' . esc_html( (string) $post->ID ) . '</strong></td></tr>';

    if ( $name ) {
        echo '<tr><th style="text-align:left;padding:3px 0;font-weight:600">Name</th>';
        echo '<td style="padding:3px 0">' . esc_html( $name ) . '</td></tr>';
    }
    if ( $email ) {
        echo '<tr><th style="text-align:left;padding:3px 0;font-weight:600">E-Mail</th>';
        echo '<td style="padding:3px 0"><a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></td></tr>';
    }
    if ( ! $name && ! $email ) {
        echo '<tr><td colspan="2" style="padding:3px 0;color:#666;font-style:italic">Manuell erfasster Beitrag</td></tr>';
    }
    echo '</tbody></table>';
}
```

- [ ] **Schritt 3: Meta-Box im Admin prüfen**

  WP-Admin → Mitmach-Beiträge → beliebigen Beitrag öffnen. Rechte Sidebar: "Einsender"-Box muss erscheinen mit "Ref.-Nr. #[ID]" und dem Hinweis "Manuell erfasster Beitrag".

- [ ] **Schritt 4: Commit**

```bash
git add theme/inc/cpt.php
git commit -S -m "feat(cpt): Einsender-Meta und Referenz-Nr.-Box für wuerde_beitrag"
```

---

## Task 3: Admin-Erweiterungen

**Files:**
- Create: `theme/inc/submissions-admin.php`
- Modify: `theme/functions.php`

**Interfaces:**
- Consumes: `wuerde_beitrag` CPT mit `pending`-Status
- Produces:
  - Untermenü "Warteliste" unter Mitmach-Beiträge (mit Pending-Zähler als Badge)
  - Dashboard-Widget mit Pending-Count + Link
  - Admin-Notice wenn Transient `wuerde_mail_fehler` gesetzt ist

- [ ] **Schritt 1: `theme/inc/submissions-admin.php` anlegen**

```php
<?php
// ABOUTME: Admin-Erweiterungen für den Einreichungs-Workflow.
// ABOUTME: Warteliste-Untermenü, Dashboard-Widget und Mail-Fehler-Benachrichtigung.

function wuerde_warteliste_submenu() {
    $count = (int) ( wp_count_posts( 'wuerde_beitrag' )->pending ?? 0 );
    $label = $count > 0
        ? 'Warteliste <span class="awaiting-mod count-' . $count . '"><span class="pending-count">' . $count . '</span></span>'
        : 'Warteliste';

    add_submenu_page(
        'edit.php?post_type=wuerde_beitrag',
        'Warteliste — Ausstehende Einreichungen',
        $label,
        'edit_posts',
        'edit.php?post_type=wuerde_beitrag&post_status=pending'
    );
}
add_action( 'admin_menu', 'wuerde_warteliste_submenu' );

function wuerde_register_dashboard_widget() {
    wp_add_dashboard_widget(
        'wuerde_einreichungen',
        'Mitmach-Einreichungen',
        'wuerde_dashboard_widget_html'
    );
}
add_action( 'wp_dashboard_setup', 'wuerde_register_dashboard_widget' );

function wuerde_dashboard_widget_html() {
    $count = (int) ( wp_count_posts( 'wuerde_beitrag' )->pending ?? 0 );
    $url   = admin_url( 'edit.php?post_type=wuerde_beitrag&post_status=pending' );

    if ( $count === 0 ) {
        echo '<p>Keine ausstehenden Einreichungen.</p>';
        return;
    }

    $singular = $count === 1 ? 'ausstehende Einreichung' : 'ausstehende Einreichungen';
    echo '<p><strong>' . esc_html( (string) $count ) . '</strong> ' . esc_html( $singular ) . '</p>';
    echo '<p><a href="' . esc_url( $url ) . '" class="button button-primary">Warteliste anzeigen</a></p>';
}

function wuerde_show_mail_error_notice() {
    $notice = get_transient( 'wuerde_mail_fehler' );
    if ( ! $notice ) {
        return;
    }
    delete_transient( 'wuerde_mail_fehler' );
    echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $notice ) . '</p></div>';
}
add_action( 'admin_notices', 'wuerde_show_mail_error_notice' );
```

- [ ] **Schritt 2: `theme/functions.php` erweitern**

  Die zwei bereits eingefügten `require_once`-Zeilen durch vier ersetzen:

```php
require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/hcaptcha.php';
require_once get_template_directory() . '/inc/submissions-admin.php';
require_once get_template_directory() . '/inc/rest-api.php';
```

  Hinweis: `rest-api.php` existiert noch nicht. Deshalb nur die ersten drei Zeilen jetzt einfügen und die vierte nach Task 4 ergänzen.

- [ ] **Schritt 3: Warteliste-Untermenü prüfen**

  WP-Admin → Mitmach-Beiträge: Untermenü "Warteliste" muss erscheinen. Klick führt zur gefilterten Liste (`post_status=pending`). Wenn keine pending-Posts existieren: leere Liste mit WP-Standard-Meldung.

- [ ] **Schritt 4: Dashboard-Widget prüfen**

  WP-Admin → Dashboard: Widget "Mitmach-Einreichungen" muss erscheinen. Bei 0 pending-Posts: "Keine ausstehenden Einreichungen."

- [ ] **Schritt 5: Commit**

```bash
git add theme/inc/submissions-admin.php theme/functions.php
git commit -S -m "feat(admin): Warteliste-Untermenü und Dashboard-Widget für Einreichungen"
```

---

## Task 4: REST-Endpoints

**Files:**
- Create: `theme/inc/rest-api.php`
- Modify: `theme/functions.php`

**Interfaces:**
- Consumes: `wuerde_verify_hcaptcha()`, `wuerde_notification_email()`, `wuerde_beitrag` CPT
- Produces:
  - `POST /wp-json/wuerde/v1/kontakt` → `{ success: true }` oder HTTP-Fehler
  - `POST /wp-json/wuerde/v1/einreichung` → `{ success: true, post_id: int }` oder HTTP-Fehler
  - Hilfsfunktionen: `wuerde_check_rate_limit(string $action): bool`, `wuerde_register_form_routes()`

- [ ] **Schritt 1: Prüfen dass Endpoints noch nicht existieren**

```bash
curl -s -o /dev/null -w "%{http_code}" \
  -X POST https://wuerde-unantastbar.de/wp-json/wuerde/v1/kontakt
# Erwartet: 404
```

- [ ] **Schritt 2: `theme/inc/rest-api.php` anlegen**

```php
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

    if ( ! wuerde_verify_hcaptcha( $request->get_param( 'captcha_token' ) ) ) {
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

    if ( ! wuerde_verify_hcaptcha( $request->get_param( 'captcha_token' ) ) ) {
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
```

- [ ] **Schritt 3: `theme/functions.php` — vierte require_once Zeile ergänzen**

  Die vier `require_once`-Zeilen müssen jetzt vollständig vorhanden sein:

```php
require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/hcaptcha.php';
require_once get_template_directory() . '/inc/submissions-admin.php';
require_once get_template_directory() . '/inc/rest-api.php';
```

- [ ] **Schritt 4: Endpoint-Registrierung prüfen**

```bash
curl -s https://wuerde-unantastbar.de/wp-json/wuerde/v1/kontakt
# Erwartet: 400 oder JSON mit "rest_missing_callback_param" (Nonce fehlt)
# Kein 404 mehr → Endpoint ist registriert
```

- [ ] **Schritt 5: Nonce-Validierung prüfen**

```bash
curl -s -X POST https://wuerde-unantastbar.de/wp-json/wuerde/v1/kontakt \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@test.de","nachricht":"Hallo","nonce":"ungueltig"}'
# Erwartet: HTTP 403, {"error":"Ungültige Anfrage."}
```

- [ ] **Schritt 6: Validierung für fehlende Pflichtfelder prüfen**

```bash
curl -s -X POST https://wuerde-unantastbar.de/wp-json/wuerde/v1/kontakt \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","nonce":"x"}'
# Erwartet: HTTP 400, {"code":"rest_missing_callback_param",...}
```

- [ ] **Schritt 7: Commit**

```bash
git add theme/inc/rest-api.php theme/functions.php
git commit -S -m "feat(api): REST-Endpoints für Kontaktformular und Mitmach-Einreichung"
```

---

## Task 5: Block `kontakt-formular`

**Files:**
- Create: `theme/blocks/kontakt-formular/block.json`
- Create: `theme/blocks/kontakt-formular/editor.asset.php`
- Create: `theme/blocks/kontakt-formular/editor.js`
- Create: `theme/blocks/kontakt-formular/render.php`
- Create: `theme/blocks/kontakt-formular/view.js`
- Create: `theme/blocks/kontakt-formular/view.css`
- Modify: `theme/inc/blocks.php`

**Interfaces:**
- Consumes: `get_option('wuerde_hcaptcha_site_key')`, `rest_url('wuerde/v1/kontakt')`, `wp_create_nonce('wuerde_kontakt')`
- Produces: Block `wuerde/kontakt-formular` im Gutenberg-Editor und Frontend

- [ ] **Schritt 1: `theme/blocks/kontakt-formular/block.json` anlegen**

```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "wuerde/kontakt-formular",
  "title": "Kontaktformular",
  "category": "wuerde",
  "description": "Kontaktformular mit hCaptcha-Schutz, sendet E-Mail an konfigurierte Adresse.",
  "keywords": ["kontakt", "formular", "email"],
  "textdomain": "wuerde-unantastbar",
  "supports": { "html": false, "align": false },
  "render": "file:./render.php",
  "viewScript": "file:./view.js",
  "viewStyle": "file:./view.css",
  "editorScript": "file:./editor.js"
}
```

- [ ] **Schritt 2: `theme/blocks/kontakt-formular/editor.asset.php` anlegen**

```php
<?php return [ 'dependencies' => [ 'wp-blocks', 'wp-element', 'wp-block-editor' ], 'version' => '1.0.0' ];
```

- [ ] **Schritt 3: `theme/blocks/kontakt-formular/editor.js` anlegen**

```js
// ABOUTME: Editor-Registrierung des Kontaktformular-Blocks.
// ABOUTME: Zeigt einen statischen Placeholder — das Formular wird server-side gerendert.

( function ( blocks, element, blockEditor ) {
    var el           = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;

    blocks.registerBlockType( 'wuerde/kontakt-formular', {
        edit: function () {
            return el(
                'div',
                useBlockProps( { style: { padding: '2rem', background: '#f0f0f0', borderRadius: '4px', textAlign: 'center' } } ),
                el( 'p', { style: { margin: 0, color: '#555' } }, '📬 Kontaktformular — wird im Frontend angezeigt' )
            );
        },
    } );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor );
```

- [ ] **Schritt 4: `theme/blocks/kontakt-formular/render.php` anlegen**

```php
<?php
// ABOUTME: Server-side Rendering des Kontaktformular-Blocks.
// ABOUTME: Bettet Nonce und REST-Endpoint-URL als data-Attribute ein.

$endpoint = esc_url( rest_url( 'wuerde/v1/kontakt' ) );
$nonce    = esc_attr( wp_create_nonce( 'wuerde_kontakt' ) );
$site_key = get_option( 'wuerde_hcaptcha_site_key', '' );

if ( $site_key ) {
    wp_enqueue_script( 'hcaptcha', 'https://js.hcaptcha.com/1/api.js', [], null, false );
}
?>
<div class="wuerde-kontakt-formular wp-block-wuerde-kontakt-formular">
    <form
        class="wuerde-kontakt-formular__form"
        data-endpoint="<?php echo $endpoint; ?>"
        data-nonce="<?php echo $nonce; ?>"
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
```

- [ ] **Schritt 5: `theme/blocks/kontakt-formular/view.js` anlegen**

```js
// ABOUTME: Kontaktformular-Submit-Handler.
// ABOUTME: Sendet Formulardaten per fetch an den REST-Endpoint und zeigt Feedback inline.

( function () {
    var form = document.querySelector( '.wuerde-kontakt-formular__form' );
    if ( ! form ) return;

    var status = form.querySelector( '.wuerde-kontakt-formular__status' );
    var btn    = form.querySelector( 'button[type="submit"]' );

    form.addEventListener( 'submit', function ( e ) {
        e.preventDefault();

        var captchaEl = form.querySelector( '[name="h-captcha-response"]' );
        var body = {
            name:          form.querySelector( '[name="name"]' ).value,
            email:         form.querySelector( '[name="email"]' ).value,
            nachricht:     form.querySelector( '[name="nachricht"]' ).value,
            captcha_token: captchaEl ? captchaEl.value : '',
            nonce:         form.dataset.nonce,
        };

        btn.disabled    = true;
        btn.textContent = '…';
        setStatus( '', false );

        fetch( form.dataset.endpoint, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify( body ),
        } )
            .then( function ( r ) {
                return r.json().then( function ( d ) { return { ok: r.ok, data: d }; } );
            } )
            .then( function ( result ) {
                if ( result.ok ) {
                    form.hidden = true;
                    setStatus( 'Vielen Dank! Deine Nachricht wurde gesendet. Wir melden uns bald bei dir.', false );
                    status.hidden = false;
                } else {
                    setStatus( result.data.error || 'Fehler beim Senden. Bitte versuche es erneut.', true );
                    btn.disabled    = false;
                    btn.textContent = 'Nachricht senden';
                }
            } )
            .catch( function () {
                setStatus( 'Netzwerkfehler. Bitte prüfe deine Internetverbindung.', true );
                btn.disabled    = false;
                btn.textContent = 'Nachricht senden';
            } );
    } );

    function setStatus( msg, isError ) {
        status.textContent = msg;
        status.hidden      = ! msg;
        status.className   = 'wuerde-kontakt-formular__status' + ( isError ? ' is-error' : ' is-success' );
    }
} )();
```

- [ ] **Schritt 6: `theme/blocks/kontakt-formular/view.css` anlegen**

```css
/* ABOUTME: Styles für das Kontaktformular auf dem Frontend. */
/* ABOUTME: Felder, Zustände und responsive Anpassung. */

.wuerde-kontakt-formular {
    max-width: 640px;
}

.wuerde-kontakt-formular__field {
    margin-bottom: 1.25rem;
}

.wuerde-kontakt-formular__field label {
    display: block;
    margin-bottom: 0.375rem;
    font-weight: 600;
}

.wuerde-kontakt-formular__field input,
.wuerde-kontakt-formular__field textarea {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 1rem;
    font-family: inherit;
    box-sizing: border-box;
}

.wuerde-kontakt-formular__field input:focus,
.wuerde-kontakt-formular__field textarea:focus {
    outline: 2px solid var(--color-teal, #00aca0);
    outline-offset: 0;
    border-color: var(--color-teal, #00aca0);
}

.wuerde-kontakt-formular__field textarea {
    resize: vertical;
}

.h-captcha {
    margin-bottom: 1.25rem;
}

.wuerde-kontakt-formular__status {
    margin-top: 1rem;
    padding: 0.75rem 1rem;
    border-radius: 4px;
}

.wuerde-kontakt-formular__status.is-error {
    background: #fce8e8;
    color: #8c1a1a;
    border: 1px solid #f5c2c2;
}

.wuerde-kontakt-formular__status.is-success {
    background: #edfaee;
    color: #1a5c1f;
    border: 1px solid #b8e5bc;
}
```

- [ ] **Schritt 7: Block in `theme/inc/blocks.php` registrieren**

  In `wuerde_register_blocks()` das Array erweitern:

```php
foreach ( [ 'mitmach-list', 'mitmach-map', 'team-grid', 'grundidee-banner', 'impressionen-teaser', 'kontakt-formular' ] as $slug ) {
```

- [ ] **Schritt 8: Block im Editor prüfen**

  Gutenberg-Editor öffnen → Block-Suche → "Kontaktformular" eingeben. Block muss erscheinen und den Placeholder anzeigen. Block auf eine Testseite einfügen und Frontend aufrufen — Formular muss dargestellt werden.

- [ ] **Schritt 9: Formular-Submit mit echtem Nonce testen**

  Aus der Seiten-Quelle die `data-nonce`-Wert auslesen, dann:

```bash
NONCE="[aus-page-source-kopieren]"
curl -s -X POST https://wuerde-unantastbar.de/wp-json/wuerde/v1/kontakt \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"Test\",\"email\":\"test@test.de\",\"nachricht\":\"Testnachricht\",\"nonce\":\"$NONCE\"}"
# Erwartet: {"success":true}
```

- [ ] **Schritt 10: Commit**

```bash
git add theme/blocks/kontakt-formular/ theme/inc/blocks.php
git commit -S -m "feat(block): Kontaktformular-Block mit REST-Submit und hCaptcha"
```

---

## Task 6: Block `mitmach-einreichung`

**Files:**
- Create: `theme/blocks/mitmach-einreichung/block.json`
- Create: `theme/blocks/mitmach-einreichung/editor.asset.php`
- Create: `theme/blocks/mitmach-einreichung/editor.js`
- Create: `theme/blocks/mitmach-einreichung/render.php`
- Create: `theme/blocks/mitmach-einreichung/view.js`
- Create: `theme/blocks/mitmach-einreichung/view.css`
- Modify: `theme/inc/blocks.php`

**Interfaces:**
- Consumes: `get_terms('wuerde_kategorie')`, `rest_url('wuerde/v1/einreichung')`, `wp_create_nonce('wuerde_einreichung')`, Leaflet (registriert in blocks.php), `wuerde_notification_email()`
- Produces: Block `wuerde/mitmach-einreichung` mit Leaflet-Karte und Post-ID-Erfolgsanzeige

- [ ] **Schritt 1: `theme/blocks/mitmach-einreichung/block.json` anlegen**

```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "wuerde/mitmach-einreichung",
  "title": "Mitmach-Einreichung",
  "category": "wuerde",
  "description": "Formular zum Einreichen von Mitmach-Beiträgen. Erstellt ausstehende Einträge zur Freigabe.",
  "keywords": ["mitmach", "einreichung", "formular", "beitrag"],
  "textdomain": "wuerde-unantastbar",
  "supports": { "html": false, "align": false },
  "render": "file:./render.php",
  "viewScript": "file:./view.js",
  "viewStyle": "file:./view.css",
  "editorScript": "file:./editor.js"
}
```

- [ ] **Schritt 2: `theme/blocks/mitmach-einreichung/editor.asset.php` anlegen**

```php
<?php return [ 'dependencies' => [ 'wp-blocks', 'wp-element', 'wp-block-editor' ], 'version' => '1.0.0' ];
```

- [ ] **Schritt 3: `theme/blocks/mitmach-einreichung/editor.js` anlegen**

```js
// ABOUTME: Editor-Registrierung des Mitmach-Einreichungs-Blocks.
// ABOUTME: Zeigt einen statischen Placeholder — das Formular wird server-side gerendert.

( function ( blocks, element, blockEditor ) {
    var el            = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;

    blocks.registerBlockType( 'wuerde/mitmach-einreichung', {
        edit: function () {
            return el(
                'div',
                useBlockProps( { style: { padding: '2rem', background: '#f0f0f0', borderRadius: '4px', textAlign: 'center' } } ),
                el( 'p', { style: { margin: 0, color: '#555' } }, '❤️ Mitmach-Einreichungsformular — wird im Frontend angezeigt' )
            );
        },
    } );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor );
```

- [ ] **Schritt 4: `theme/blocks/mitmach-einreichung/render.php` anlegen**

```php
<?php
// ABOUTME: Server-side Rendering des Mitmach-Einreichungs-Blocks.
// ABOUTME: Lädt Kategorien, Leaflet und bettet Konfiguration als data-Attribute ein.

$endpoint     = esc_url( rest_url( 'wuerde/v1/einreichung' ) );
$nonce        = esc_attr( wp_create_nonce( 'wuerde_einreichung' ) );
$site_key     = get_option( 'wuerde_hcaptcha_site_key', '' );
$notify_email = wuerde_notification_email();
$crown_url    = esc_url( get_template_directory_uri() . '/assets/krone-white.png' );

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
?>
<div class="wuerde-mitmach-einreichung wp-block-wuerde-mitmach-einreichung">
    <form
        class="wuerde-mitmach-einreichung__form"
        data-endpoint="<?php echo $endpoint; ?>"
        data-nonce="<?php echo $nonce; ?>"
        data-notify-email="<?php echo esc_attr( $notify_email ); ?>"
        data-crown="<?php echo $crown_url; ?>"
        novalidate
    >
        <div class="wuerde-mitmach-einreichung__field">
            <label for="wuerde-einr-name">Name <span aria-hidden="true">*</span></label>
            <input type="text" id="wuerde-einr-name" name="name" required autocomplete="name">
        </div>
        <div class="wuerde-mitmach-einreichung__field">
            <label for="wuerde-einr-email">E-Mail-Adresse <span aria-hidden="true">*</span></label>
            <input type="email" id="wuerde-einr-email" name="email" required autocomplete="email">
        </div>
        <div class="wuerde-mitmach-einreichung__field">
            <label for="wuerde-einr-titel">Titel <span aria-hidden="true">*</span></label>
            <input type="text" id="wuerde-einr-titel" name="titel" required
                   placeholder="z. B. Kunstprojekt im Bürgerpark">
        </div>
        <div class="wuerde-mitmach-einreichung__field">
            <label for="wuerde-einr-beschreibung">Beschreibung <span aria-hidden="true">*</span></label>
            <textarea id="wuerde-einr-beschreibung" name="beschreibung" rows="8" required
                      placeholder="Was habt ihr gemacht? Was war besonders?"></textarea>
        </div>
        <div class="wuerde-mitmach-einreichung__field">
            <label for="wuerde-einr-kategorie">Kategorie</label>
            <select id="wuerde-einr-kategorie" name="kategorie_id">
                <option value="">— Bitte wählen (optional) —</option>
                <?php foreach ( (array) $kategorien as $term ) : ?>
                <option value="<?php echo esc_attr( (string) $term->term_id ); ?>">
                    <?php echo esc_html( $term->name ); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="wuerde-mitmach-einreichung__field">
            <label for="wuerde-einr-ort">Ort</label>
            <input type="text" id="wuerde-einr-ort" name="ort" autocomplete="off"
                   placeholder="z. B. München">
        </div>
        <details class="wuerde-mitmach-einreichung__karte-toggle">
            <summary>Ort auf Karte auswählen (optional)</summary>
            <div id="wuerde-einr-map"
                 style="height:320px;border-radius:4px;border:1px solid #ddd;margin-top:8px"></div>
        </details>
        <input type="hidden" name="lat" value="">
        <input type="hidden" name="lng" value="">
        <?php if ( $site_key ) : ?>
        <div class="h-captcha" data-sitekey="<?php echo esc_attr( $site_key ); ?>"></div>
        <?php endif; ?>
        <p class="wuerde-mitmach-einreichung__datei-hinweis">
            Fotos und Dokumente bitte per E-Mail an
            <a href="mailto:<?php echo esc_attr( $notify_email ); ?>"><?php echo esc_html( $notify_email ); ?></a>
            senden — nach dem Absenden erhältst du eine Referenznummer.
        </p>
        <button type="submit" class="btn btn--primary">Beitrag einreichen</button>
        <div class="wuerde-mitmach-einreichung__status" aria-live="polite" hidden></div>
    </form>
    <div class="wuerde-mitmach-einreichung__erfolg" hidden></div>
</div>
```

- [ ] **Schritt 5: `theme/blocks/mitmach-einreichung/view.js` anlegen**

```js
// ABOUTME: Mitmach-Einreichungsformular: Submit-Handler, Leaflet-Karte und Erfolgsanzeige.
// ABOUTME: Die Karte wird erst beim Öffnen des <details>-Elements initialisiert.

( function () {
    var wrapper = document.querySelector( '.wuerde-mitmach-einreichung' );
    if ( ! wrapper ) return;

    var form   = wrapper.querySelector( '.wuerde-mitmach-einreichung__form' );
    var erfolg = wrapper.querySelector( '.wuerde-mitmach-einreichung__erfolg' );
    var status = form.querySelector( '.wuerde-mitmach-einreichung__status' );
    var btn    = form.querySelector( 'button[type="submit"]' );
    var latEl  = form.querySelector( '[name="lat"]' );
    var lngEl  = form.querySelector( '[name="lng"]' );
    var ortEl  = form.querySelector( '[name="ort"]' );
    var mapEl  = document.getElementById( 'wuerde-einr-map' );
    var details = form.querySelector( '.wuerde-mitmach-einreichung__karte-toggle' );

    // Karte erst beim Öffnen des <details> laden.
    var mapReady = false;
    if ( details && mapEl && typeof L !== 'undefined' ) {
        details.addEventListener( 'toggle', function () {
            if ( details.open && ! mapReady ) {
                initMap();
            }
        } );
    }

    function initMap() {
        mapReady = true;
        var map = L.map( mapEl, { scrollWheelZoom: false } ).setView( [ 51.2, 10.4 ], 6 );
        L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap-Mitwirkende',
            maxZoom: 19,
        } ).addTo( map );
        setTimeout( function () { map.invalidateSize(); }, 200 );

        var pinIcon = L.divIcon( {
            className:   'mitmach-map__pin',
            html:        '<div class="mitmach-map__pin-dot" style="background:#00ACA0"><img src="' + form.dataset.crown + '" alt="" aria-hidden="true"></div>',
            iconSize:    [ 32, 32 ],
            iconAnchor:  [ 16, 16 ],
        } );

        var marker = null;

        map.on( 'click', function ( e ) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            latEl.value = lat.toFixed( 6 );
            lngEl.value = lng.toFixed( 6 );
            if ( marker ) {
                marker.setLatLng( e.latlng );
            } else {
                marker = L.marker( e.latlng, { icon: pinIcon } ).addTo( map );
            }
            reverseGeocode( lat, lng );
        } );
    }

    function reverseGeocode( lat, lng ) {
        var url = 'https://nominatim.openstreetmap.org/reverse?lat=' + lat + '&lon=' + lng + '&format=json&accept-language=de';
        fetch( url )
            .then( function ( r ) { return r.json(); } )
            .then( function ( data ) {
                var a    = data.address || {};
                var city = a.city || a.town || a.village || a.municipality || a.county || '';
                // Ort nur vorausfüllen wenn Feld noch leer.
                if ( city && ! ortEl.value ) {
                    ortEl.value = city;
                }
            } )
            .catch( function () {} );
    }

    // Formular-Submit
    form.addEventListener( 'submit', function ( e ) {
        e.preventDefault();

        var captchaEl = form.querySelector( '[name="h-captcha-response"]' );
        var body = {
            name:          form.querySelector( '[name="name"]' ).value,
            email:         form.querySelector( '[name="email"]' ).value,
            titel:         form.querySelector( '[name="titel"]' ).value,
            beschreibung:  form.querySelector( '[name="beschreibung"]' ).value,
            kategorie_id:  parseInt( form.querySelector( '[name="kategorie_id"]' ).value, 10 ) || 0,
            ort:           ortEl.value,
            lat:           parseFloat( latEl.value ) || 0,
            lng:           parseFloat( lngEl.value ) || 0,
            captcha_token: captchaEl ? captchaEl.value : '',
            nonce:         form.dataset.nonce,
        };

        btn.disabled    = true;
        btn.textContent = '…';
        setStatus( '', false );

        fetch( form.dataset.endpoint, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify( body ),
        } )
            .then( function ( r ) {
                return r.json().then( function ( d ) { return { ok: r.ok, data: d }; } );
            } )
            .then( function ( result ) {
                if ( result.ok && result.data.post_id ) {
                    zeigErfolg( result.data.post_id );
                } else {
                    setStatus( result.data.error || 'Fehler. Bitte versuche es erneut.', true );
                    btn.disabled    = false;
                    btn.textContent = 'Beitrag einreichen';
                }
            } )
            .catch( function () {
                setStatus( 'Netzwerkfehler. Bitte prüfe deine Internetverbindung.', true );
                btn.disabled    = false;
                btn.textContent = 'Beitrag einreichen';
            } );
    } );

    function zeigErfolg( postId ) {
        form.hidden = true;
        var email   = form.dataset.notifyEmail;
        var subject = encodeURIComponent( 'Anhänge zu Einreichung #' + postId );
        var bodyTxt = encodeURIComponent( 'Bitte füge diesem E-Mail deine Fotos oder Dokumente als Anhang bei.' );
        var mailto  = 'mailto:' + email + '?subject=' + subject + '&body=' + bodyTxt;

        erfolg.innerHTML =
            '<p><strong>Vielen Dank!</strong> Dein Beitrag wurde gespeichert (Referenz <strong>#' +
            postId + '</strong>).</p>' +
            '<p>Um Fotos oder Dokumente beizufügen, sende uns eine E-Mail:</p>' +
            '<a href="' + mailto + '" class="btn btn--secondary">📎 Dateien per E-Mail zusenden</a>';
        erfolg.hidden = false;
    }

    function setStatus( msg, isError ) {
        status.textContent = msg;
        status.hidden      = ! msg;
        status.className   = 'wuerde-mitmach-einreichung__status' + ( isError ? ' is-error' : ' is-success' );
    }
} )();
```

- [ ] **Schritt 6: `theme/blocks/mitmach-einreichung/view.css` anlegen**

```css
/* ABOUTME: Styles für das Mitmach-Einreichungsformular auf dem Frontend. */
/* ABOUTME: Felder, Karten-Toggle, Zustände und Erfolgsanzeige. */

.wuerde-mitmach-einreichung {
    max-width: 640px;
}

.wuerde-mitmach-einreichung__field {
    margin-bottom: 1.25rem;
}

.wuerde-mitmach-einreichung__field label {
    display: block;
    margin-bottom: 0.375rem;
    font-weight: 600;
}

.wuerde-mitmach-einreichung__field input,
.wuerde-mitmach-einreichung__field textarea,
.wuerde-mitmach-einreichung__field select {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 1rem;
    font-family: inherit;
    box-sizing: border-box;
}

.wuerde-mitmach-einreichung__field input:focus,
.wuerde-mitmach-einreichung__field textarea:focus,
.wuerde-mitmach-einreichung__field select:focus {
    outline: 2px solid var(--color-teal, #00aca0);
    outline-offset: 0;
    border-color: var(--color-teal, #00aca0);
}

.wuerde-mitmach-einreichung__field textarea {
    resize: vertical;
}

.wuerde-mitmach-einreichung__karte-toggle {
    margin-bottom: 1.25rem;
}

.wuerde-mitmach-einreichung__karte-toggle > summary {
    cursor: pointer;
    color: var(--color-teal, #00aca0);
    font-weight: 600;
}

.wuerde-mitmach-einreichung__datei-hinweis {
    font-size: 0.875rem;
    color: #555;
    margin-bottom: 1.25rem;
}

.h-captcha {
    margin-bottom: 1.25rem;
}

.wuerde-mitmach-einreichung__status {
    margin-top: 1rem;
    padding: 0.75rem 1rem;
    border-radius: 4px;
}

.wuerde-mitmach-einreichung__status.is-error {
    background: #fce8e8;
    color: #8c1a1a;
    border: 1px solid #f5c2c2;
}

.wuerde-mitmach-einreichung__status.is-success {
    background: #edfaee;
    color: #1a5c1f;
    border: 1px solid #b8e5bc;
}

.wuerde-mitmach-einreichung__erfolg {
    padding: 1.5rem;
    background: #edfaee;
    border: 1px solid #b8e5bc;
    border-radius: 4px;
}

.wuerde-mitmach-einreichung__erfolg p {
    margin-bottom: 1rem;
}
```

- [ ] **Schritt 7: Block in `theme/inc/blocks.php` registrieren**

  Array in `wuerde_register_blocks()` auf alle sechs Slugs erweitern:

```php
foreach ( [ 'mitmach-list', 'mitmach-map', 'team-grid', 'grundidee-banner', 'impressionen-teaser', 'kontakt-formular', 'mitmach-einreichung' ] as $slug ) {
```

- [ ] **Schritt 8: Block im Editor prüfen**

  Gutenberg-Editor öffnen → "Mitmach-Einreichung" suchen. Block auf Testseite einfügen, Frontend aufrufen. Alle Felder und der Karten-Toggle müssen erscheinen. `<details>` öffnen — Leaflet-Karte muss erscheinen.

- [ ] **Schritt 9: End-to-End-Einreichung testen**

  Nonce aus Seitenquellcode kopieren, dann:

```bash
NONCE="[aus-page-source-kopieren]"
curl -s -X POST https://wuerde-unantastbar.de/wp-json/wuerde/v1/einreichung \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"Test Einsender\",\"email\":\"test@test.de\",\"titel\":\"Mein Testbeitrag\",\"beschreibung\":\"Eine ausführliche Beschreibung.\",\"nonce\":\"$NONCE\"}"
# Erwartet: {"success":true,"post_id":NNN}
```

  Im WP-Admin → Mitmach-Beiträge → Warteliste: Neuer Eintrag mit Status "Ausstehend". Einsender-Box in der rechten Sidebar zeigt Ref.-Nr., Name und E-Mail.

- [ ] **Schritt 10: Commit**

```bash
git add theme/blocks/mitmach-einreichung/ theme/inc/blocks.php
git commit -S -m "feat(block): Mitmach-Einreichungs-Block mit Leaflet-Karte und Referenz-Nr."
```
