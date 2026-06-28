# Kontaktformular & Mitmach-Einreichung

**Datum:** 2026-06-28
**Branch:** 23-startseite-design-ueberfuehren (oder neuer Feature-Branch)

---

## Überblick

Zwei unabhängige Gutenberg-Blöcke für die Website, ohne externe Form-Plugins:

1. **`kontakt-formular`** — Allgemeines Kontaktformular auf `/kontakt/`
2. **`mitmach-einreichung`** — Formular zum Einreichen von Mitmach-Beiträgen auf einer separaten Seite

Beide Formulare übermitteln per **WP REST API** (Ansatz B), speichern keine Dateien und sind gegen XSS, CSRF, Bot-Spam und Rate-Abuse abgesichert.

---

## Dateistruktur

### Neue Dateien

```
theme/inc/
  hcaptcha.php          ← hCaptcha-Verifikation (eine Hilfsfunktion)
  rest-api.php          ← REST-Endpoints für beide Formulare
  settings.php          ← Admin-Einstellungsseite
  submissions-admin.php ← Warteliste-Untermenü, Dashboard-Widget, Mail-Fehler-Notice

theme/blocks/
  kontakt-formular/
    block.json
    editor.asset.php
    editor.js
    render.php
    view.js
    view.css
  mitmach-einreichung/
    block.json
    editor.asset.php
    editor.js
    render.php
    view.js
    view.css
```

### Geänderte Dateien

| Datei | Änderung |
|-------|----------|
| `theme/functions.php` | `require_once` für vier neue inc-Dateien |
| `theme/inc/cpt.php` | Post-Meta `wuerde_einreichung_name` und `wuerde_einreichung_email` registrieren |
| `theme/inc/blocks.php` | Zwei neue Blöcke registrieren |

---

## REST-Endpoints

### `POST /wp-json/wuerde/v1/kontakt`

**Parameter:**

| Feld | Typ | Sanitisierung |
|------|-----|---------------|
| `name` | string | `sanitize_text_field` |
| `email` | string | `sanitize_email` |
| `nachricht` | string | `sanitize_textarea_field` |
| `captcha_token` | string | `sanitize_text_field` |
| `nonce` | string | `sanitize_text_field` |

**Ablauf:** Nonce-Prüfung → Rate-Limit-Check (1/h pro IP) → hCaptcha-Verifikation → `wp_mail()` an konfigurierte Adresse → Erfolg `{ success: true }` oder Fehler mit HTTP-Statuscode.

Bei `wp_mail()`-Fehler: Admin-Notice via Transient.

---

### `POST /wp-json/wuerde/v1/einreichung`

**Parameter:**

| Feld | Typ | Sanitisierung |
|------|-----|---------------|
| `name` | string | `sanitize_text_field` |
| `email` | string | `sanitize_email` |
| `titel` | string | `sanitize_text_field` |
| `beschreibung` | string | `sanitize_textarea_field` |
| `kategorie_id` | integer | `(int)` + `term_exists()` |
| `ort` | string | `sanitize_text_field` |
| `lat` | number | `(float)` + Range-Check −90..90 |
| `lng` | number | `(float)` + Range-Check −180..180 |
| `captcha_token` | string | `sanitize_text_field` |
| `nonce` | string | `sanitize_text_field` |

**Ablauf:** Nonce-Prüfung → Rate-Limit-Check (1/h pro IP) → hCaptcha-Verifikation → `wp_insert_post(['post_status' => 'pending'])` → Post-Meta speichern → `wp_mail()` Benachrichtigung → `{ success: true, post_id: 123 }`.

Bei `wp_mail()`-Fehler: Admin-Notice via Transient.

---

## Sicherheit

### XSS-Schutz (zwei unabhängige Schichten)

- **Schicht 1 — Eingang:** Sanitisierung per `sanitize_text_field()` / `sanitize_textarea_field()` via REST-Endpoint-Schema (`sanitize_callback`), läuft vor dem Handler-Code. Alle HTML-Tags werden durch `strip_tags()` entfernt.
- **Schicht 2 — Ausgabe:** `esc_html()` / `esc_attr()` überall im Admin. Benachrichtigungs-E-Mails als Plain-Text.

### CSRF-Schutz

WP-Nonce wird im gerenderten Formular eingebettet (`wp_create_nonce('wuerde_kontakt')` / `'wuerde_einreichung'`). Der REST-Handler prüft ihn als erste Aktion via `wp_verify_nonce()`. Ohne gültigen Nonce: HTTP 403, keine weitere Verarbeitung.

### Bot-Prävention (hCaptcha)

Verifikation per `wp_remote_post()` an `hcaptcha.com/siteverify` — nach Nonce-Check, vor Datenbankzugriff. Schlägt die Verifikation fehl: HTTP 400. Ist kein Site-Key konfiguriert, wird hCaptcha übersprungen (graceful degradation).

### Rate Limiting

Transient-basiert pro IP-Adresse, 1h TTL:

| Formular | Limit |
|----------|-------|
| Kontakt | 1 Anfrage / Stunde |
| Mitmach-Einreichung | 1 Einreichung / Stunde |

### E-Mail-Header-Injection

`sanitize_text_field()` entfernt `\r` und `\n` aus allen Feldern die in Mail-Headern landen (Name, Betreff).

### SQL-Injection

Kein direkter `$wpdb`-Zugriff. Alle Datenbankoperationen über WP-API-Funktionen (`wp_insert_post`, `update_post_meta`, `wp_set_post_terms`) mit internen Prepared Statements.

---

## Block: `kontakt-formular`

### Felder

- Name (Pflicht)
- E-Mail (Pflicht)
- Nachricht (Pflicht)
- hCaptcha-Widget
- Absenden-Button

### UX

Submit per `fetch()` ohne Page-Reload. Inline-Anzeige von Lade-, Erfolgs- und Fehlerzustand.

---

## Block: `mitmach-einreichung`

### Felder

- Name, E-Mail (Pflicht, intern — als Post-Meta gespeichert, nicht öffentlich)
- Titel (Pflicht → `post_title`)
- Beschreibung (Pflicht → `post_content`)
- Kategorie (Dropdown der bestehenden `wuerde_kategorie`-Terms)
- Ort (Freitextfeld → `wuerde_ort`-Taxonomy)
- **Karte** (Leaflet + Nominatim, eingeklappt via `<details>`) — füllt Ort, Lat, Lng automatisch

### Hinweis auf Datei-Upload

Unterhalb des Formulars: *"Fotos und Dokumente bitte per E-Mail an [Benachrichtigungs-E-Mail] senden."*

### Erfolgsanzeige nach dem Absenden

```
Vielen Dank! Deine Einreichung wurde gespeichert (Referenz #123).

[📎 Dateien per E-Mail zusenden →]
```

Der Button öffnet:
```
mailto:{benachrichtigungs_email}
  ?subject=Anhänge zu Einreichung %23{post_id}
  &body=Bitte füge diesem E-Mail deine Fotos oder Dokumente als Anhang bei.
```

Die Empfängeradresse stammt aus der Einstellungsseite.

---

## Datenmodell: `wuerde_beitrag` (Erweiterung)

Neue Post-Meta-Felder (nicht öffentlich, kein `show_in_rest`):

| Meta-Key | Inhalt |
|----------|--------|
| `wuerde_einreichung_name` | Name des Einsenders |
| `wuerde_einreichung_email` | E-Mail des Einsenders |

Post-Status bei Einreichung: `pending` (WP-nativer Status).

---

## Admin-Erweiterungen

### Einstellungsseite

Unter **Einstellungen → Formulare**:

| Einstellung | Option-Key |
|-------------|------------|
| hCaptcha Site Key | `wuerde_hcaptcha_site_key` |
| hCaptcha Secret Key | `wuerde_hcaptcha_secret_key` |
| Benachrichtigungs-E-Mail | `wuerde_notification_email` |

Fallback für Benachrichtigungs-E-Mail: `get_option('admin_email')`.

### Warteliste-Untermenü

Unter **Mitmach-Beiträge → Warteliste**: Link auf `edit.php?post_type=wuerde_beitrag&post_status=pending`. Zeigt nur ausstehende Einreichungen.

### Dashboard-Widget

Zeigt Anzahl ausstehender Einreichungen (`post_status=pending`) und einen direkten Link zur Warteliste.

### Meta-Box im Beitrags-Editor

Zeigt Einsender-Informationen:

- **Referenz-Nr.:** `#123`
- **Name:** Max Mustermann
- **E-Mail:** max@example.com

### Admin-Notice bei Mail-Fehler

Wenn `wp_mail()` `false` zurückgibt: Transient `wuerde_mail_fehler` setzen. Bei `admin_notices`: Notice anzeigen und Transient löschen.

---

## hCaptcha (`inc/hcaptcha.php`)

```php
// Gibt true zurück wenn Verifikation erfolgreich oder kein Secret-Key konfiguriert.
function wuerde_verify_hcaptcha( string $token ): bool
```

Intern: `wp_remote_post( 'https://hcaptcha.com/siteverify', [...] )`.

---

## E-Mail-Benachrichtigungen

### Kontaktformular → Admin

- **An:** konfigurierte Benachrichtigungs-E-Mail
- **Betreff:** `Neue Kontaktanfrage von {name}`
- **Inhalt:** Name, E-Mail, Nachricht (Plain-Text)

### Mitmach-Einreichung → Admin

- **An:** konfigurierte Benachrichtigungs-E-Mail
- **Betreff:** `Neue Einreichung: {titel} (Referenz #123)`
- **Inhalt:** Referenz-Nr., Name, E-Mail, Titel, Beschreibung, Kategorie, Ort (Plain-Text)
