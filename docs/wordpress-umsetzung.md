# WordPress-Umsetzungsplan

Mapping der HTML-Entwürfe auf WordPress-Funktionen und -Konzepte.
Zeigt, was nativ abgedeckt wird, was ein Plugin braucht, und was custom entwickelt werden muss.

---

## Überblick

| Bereich | WordPress-Ansatz | Aufwand |
|---------|-----------------|---------|
| Statische Seiten (Grundidee, Über uns, Kontakt) | Seiten + Gutenberg | gering |
| Startseite | Statische Startseite + Gutenberg | gering |
| Navigation mit Dropdown | WP Nav Menus | gering |
| Kontaktformular + Datei-Upload | Plugin: Contact Form 7 | gering |
| Beiträge (Kacheln auf Mach mit) | Custom Post Type + Custom Taxonomy | mittel |
| Kategorie-Accordion | Theme-Template + WP_Query | mittel |
| Suche / Filterung | AJAX + WP_Query | mittel |
| Deutschlandkarte mit Pins | Plugin (Leaflet) + Custom Fields | mittel–hoch |
| Team-Übersicht | Custom Post Type oder Gutenberg | mittel |
| Theme / CSS | Custom Child Theme | hoch |

---

## 1. Seiten-Struktur

### Was WordPress nativ liefert

Alle fünf Seiten werden als WordPress-**Seiten** (`page`) angelegt:

| Seite | WordPress-Slug | Template |
|-------|---------------|----------|
| Startseite | `/` | `front-page.php` |
| Grundidee | `/grundidee/` | `page-grundidee.php` oder Gutenberg |
| Über uns | `/ueber-uns/` | `page-ueber-uns.php` oder Gutenberg |
| Mach mit | `/mach-mit/` | `page-mach-mit.php` (custom) |
| Kontakt | `/kontakt/` | `page-kontakt.php` oder Gutenberg |

Unter **Einstellungen → Lesen** wird die Startseite als statische Seite konfiguriert.

### Startseite nicht im Menü

WordPress erlaubt es, einzelne Seiten aus dem Navigationsmenü herauszulassen.
Die Startseite wird im Menü-Editor einfach nicht hinzugefügt — der Rückweg erfolgt
über das Logo (`home_url()`).

---

## 2. Navigation

### WordPress Nav Menus — native Lösung

```
Darstellung → Menüs → Hauptnavigation
```

- Menüpunkte: Grundidee, Über uns, Mach mit, Kontakt
- **„Mach mit" mit Untermenü**: In WordPress einfach untergeordnete Menüpunkte anlegen.
  Das Theme rendert sie als Dropdown via `wp_nav_menu()`.
- Das Dropdown-Verhalten (CSS + minimales JS) wird im Theme implementiert.

### Custom Walker (optional)

Wenn das Standard-Markup (`<ul><li>`) nicht ausreicht (z.B. für `<details>`/`<summary>`
aus den Entwürfen), wird ein Custom Nav Walker implementiert. Das ist ~50 Zeilen PHP.

---

## 3. Kontaktformular

### Plugin: Contact Form 7 (kostenlos)

Contact Form 7 deckt alle Anforderungen ohne weitere Konfiguration:

- Textfelder (Name, E-Mail, Betreff, Nachricht) — nativ
- **Datei-Upload** — nativ via `[file]`-Tag mit konfigurierbaren Dateitypen und -größen
- Spam-Schutz — CF7 + Flamingo (kostenloses Companion-Plugin) für Nachrichtenarchivierung
- Ausgabe der Erfolgsmeldung — über CF7-Response-Klassen styled

```
Stylesheet-Hook: .wpcf7-response-output { ... }
```

CF7 erzeugt valides, zugängliches HTML; unsere CSS-Klassen werden per
`wpcf7_form_elements`-Filter auf das Formular-Markup angewendet.

**Keine Alternative nötig.** CF7 ist für diesen Anwendungsfall der Standard.

---

## 4. Beiträge (Kacheln auf „Mach mit")

Dies ist der inhaltlich komplexeste Bereich. Jede Kachel auf der Mach-mit-Seite
entspricht einem **Beitrag**, der:
- ein Bild hat (Vorschaubild / Featured Image)
- einer Kategorie zugeordnet ist (Kirchengemeinden, Bildung, …)
- einem Ort zugeordnet ist (Köln, Bonn, …)
- Freitext enthält

### Custom Post Type: `wuerde_beitrag`

Ein eigener Beitragstyp trennt die Vereinsbeiträge von regulären Blog-Posts.
Wird in `functions.php` registriert:

```php
register_post_type('wuerde_beitrag', [
    'labels'        => ['name' => 'Beiträge', 'singular_name' => 'Beitrag'],
    'public'        => true,
    'has_archive'   => false,
    'supports'      => ['title', 'editor', 'thumbnail', 'excerpt'],
    'show_in_rest'  => true,
    'menu_icon'     => 'dashicons-heart',
]);
```

### Custom Taxonomy: `wuerde_kategorie`

Ersetzt WordPress-Standardkategorien für diesen CPT:

```php
register_taxonomy('wuerde_kategorie', 'wuerde_beitrag', [
    'labels'        => ['name' => 'Kategorien', 'singular_name' => 'Kategorie'],
    'hierarchical'  => true,   // wie Kategorien, nicht Tags
    'show_in_rest'  => true,
]);
```

Vordefinierte Terme (werden einmalig per Code oder manuell angelegt):
- `kirchengemeinden` → Kirchengemeinden
- `bildung` → Bildungseinrichtungen
- `kommunal` → Kommunale Einrichtungen
- `handwerk` → Handwerk & Industrie
- `gesundheit` → Gesundheitswesen
- `sonstiges` → Sonstiges

### Custom Taxonomy: `wuerde_ort`

Für die regionale Zuordnung (Köln, Hamburg, …):

```php
register_taxonomy('wuerde_ort', 'wuerde_beitrag', [
    'labels'      => ['name' => 'Orte', 'singular_name' => 'Ort'],
    'hierarchical' => false,
    'show_in_rest' => true,
]);
```

### Koordinaten für die Karte

Jedem `wuerde_beitrag` werden Breitengrad und Längengrad als Custom Fields zugewiesen.
Das geht am einfachsten mit **Advanced Custom Fields (ACF, kostenlose Version)**:

```
Feldgruppe: "Kartenposition"
  - Breitengrad (lat) — Zahl
  - Längengrad (lng)  — Zahl
```

In der Karten-Implementierung werden diese Werte per WP_Query abgefragt und als
JSON in die Seite injiziert (siehe Abschnitt 6).

---

## 5. Kategorie-Accordion auf „Mach mit"

### Theme-Template: `page-mach-mit.php`

Das Accordion wird server-seitig aus den Taxonomie-Termen aufgebaut:

```php
$terms = get_terms(['taxonomy' => 'wuerde_kategorie', 'hide_empty' => true]);

foreach ($terms as $term) {
    $posts = new WP_Query([
        'post_type'      => 'wuerde_beitrag',
        'posts_per_page' => -1,
        'tax_query'      => [[
            'taxonomy' => 'wuerde_kategorie',
            'field'    => 'slug',
            'terms'    => $term->slug,
        ]],
    ]);

    // Accordion-Item ausgeben
    // Kacheln pro Post ausgeben
}
```

Das HTML-Markup der Kacheln ist identisch mit den Entwürfen; WordPress befüllt
es dynamisch statt statisch.

### Keine Plugin-Abhängigkeit

Das Accordion verwendet natives `<details>`/`<summary>` HTML — kein JavaScript-Plugin
nötig. Der Browser erledigt das Auf-/Zuklappen.

---

## 6. Suche und Filterung

Die Entwürfe zeigen zwei Interaktionen:
1. **Texteingabe** filtert Kacheln nach Stichwort
2. **Kategorie-Chips** filtern nach Taxonomie-Term

### Ansatz: AJAX + WP_Query

Für eine saubere, SEO-freundliche Lösung ohne Page-Reload:

```javascript
// Im Theme: search-filter.js
async function filterEntries(term, category) {
    const response = await fetch(wpAjax.ajaxurl, {
        method: 'POST',
        body: new URLSearchParams({
            action: 'wuerde_filter',
            term,
            category,
            nonce: wpAjax.nonce,
        }),
    });
    const html = await response.json();
    document.getElementById('entries-container').innerHTML = html.data;
}
```

```php
// In functions.php
add_action('wp_ajax_nopriv_wuerde_filter', 'wuerde_filter_handler');
add_action('wp_ajax_wuerde_filter', 'wuerde_filter_handler');

function wuerde_filter_handler() {
    check_ajax_referer('wuerde_filter', 'nonce');

    $args = [
        'post_type'      => 'wuerde_beitrag',
        'posts_per_page' => -1,
        's'              => sanitize_text_field($_POST['term'] ?? ''),
    ];

    if (!empty($_POST['category']) && $_POST['category'] !== 'all') {
        $args['tax_query'] = [[
            'taxonomy' => 'wuerde_kategorie',
            'field'    => 'slug',
            'terms'    => sanitize_key($_POST['category']),
        ]];
    }

    $query = new WP_Query($args);
    ob_start();
    // Kacheln-Template rendern
    wp_send_json_success(ob_get_clean());
}
```

**Alternative (einfacher, kein AJAX):** Formulare mit GET-Parametern + normale
Seitenladung. Ausreichend für die erwartete Nutzungsfrequenz, aber kein sofortiges
Filtern. Empfehlung: AJAX-Variante, da die UX-Erwartung aus den Entwürfen klar ist.

---

## 7. Deutschlandkarte

Dies ist der technisch aufwändigste Einzelbestandteil.

### Plugin: WP Leaflet Map (kostenlos)

[WP Leaflet Map](https://wordpress.org/plugins/wp-leaflet-map/) integriert
Leaflet.js in WordPress ohne externe API-Abhängigkeit (kein Google-API-Key nötig).

**Einschränkung:** Das Plugin ist auf einfache Marker-Karten ausgelegt.
Für die geplante Interaktion (Klick auf Pin öffnet Beitrag, AJAX-Filter aktualisiert
die Karte) reicht das Plugin-Interface nicht aus.

### Empfohlener Ansatz: Leaflet.js direkt, Pins aus WP_Query

```php
// Im Template: Koordinaten + Metadaten aller Beiträge als JSON ausgeben
$posts = get_posts(['post_type' => 'wuerde_beitrag', 'numberposts' => -1]);
$map_data = array_map(function($post) {
    return [
        'title'    => $post->post_title,
        'lat'      => (float) get_field('lat', $post->ID),
        'lng'      => (float) get_field('lng', $post->ID),
        'category' => wp_get_post_terms($post->ID, 'wuerde_kategorie')[0]->slug ?? '',
        'ort'      => wp_get_post_terms($post->ID, 'wuerde_ort')[0]->name ?? '',
    ];
}, $posts);

wp_localize_script('wuerde-map', 'mapData', $map_data);
```

```javascript
// map.js
const map = L.map('map-container').setView([51.2, 10.4], 6); // Deutschland-Mitte
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

mapData.forEach(entry => {
    if (!entry.lat || !entry.lng) return;
    const marker = L.circleMarker([entry.lat, entry.lng], { /* Stil */ });
    marker.bindPopup(`<strong>${entry.title}</strong><br>${entry.ort}`);
    marker.addTo(map);
});
```

OpenStreetMap als Kartenbasis ist kostenlos und datenschutzkonform (kein Google).

**Aufwand:** ~1 Tag für Grundfunktion, ~0,5 Tage für Styling und Filterverbindung.

---

## 8. Team-Übersicht (Über uns)

### Option A: Custom Post Type `wuerde_team` (empfohlen)

Ermöglicht dem Verein, Teammitglieder im WP-Admin zu pflegen:

```php
register_post_type('wuerde_team', [
    'labels'   => ['name' => 'Team', 'singular_name' => 'Person'],
    'public'   => false,  // keine eigene Archivseite
    'supports' => ['title', 'editor', 'thumbnail'],
    'show_ui'  => true,
]);
```

ACF-Felder pro Person: Rolle/Funktion (Text).

Im Template:
```php
$members = get_posts(['post_type' => 'wuerde_team', 'numberposts' => -1, 'orderby' => 'menu_order']);
```

### Option B: Gutenberg-Blöcke (einfacher, weniger flexibel)

Die Team-Seite wird komplett im Block Editor aufgebaut — Spalten-Block mit
je einem Bild-Block + Absatz. Kein CPT nötig, aber Änderungen erfordern
Gutenberg-Kenntnisse statt eines einfachen Formulareintrags.

**Empfehlung:** Option A, da Redakteure ohne technische Kenntnisse Personen
hinzufügen oder entfernen können müssen.

---

## 9. Theme

### Strategie: Custom Theme (kein Page Builder)

Ein Page Builder (Elementor, Divi) würde die Entwürfe nicht sauber abbilden
und erzeugt erheblichen Markup-Overhead. Das Theme wird als Custom Theme
auf Basis der HTML-Entwürfe entwickelt.

**Minimale Template-Hierarchie:**

```
wuerde-theme/
├── style.css           ← Theme-Header + Haupt-CSS (aus assets/style.css)
├── functions.php       ← Enqueues, CPT-Registrierung, AJAX-Handler
├── header.php          ← Nav, Site-Header
├── footer.php          ← Footer
├── front-page.php      ← Startseite
├── page.php            ← Fallback für alle Seiten
├── page-grundidee.php  ← Grundidee (oder Gutenberg reicht)
├── page-ueber-uns.php  ← Über uns mit Team-Query
├── page-mach-mit.php   ← Mach mit (Karte, Suche, Accordion)
├── page-kontakt.php    ← Kontakt mit CF7
└── template-parts/
    ├── entry-card.php  ← Kachel-Template (wiederverwendbar)
    └── team-card.php   ← Team-Karte
```

### CSS

`assets/style.css` aus den Entwürfen wird als `style.css` im Theme verwendet.
WordPress benötigt den Theme-Header-Kommentar oben in der Datei:

```css
/*
Theme Name: Würde unantastbar
Version: 1.0.0
*/
```

Das CSS wird über `wp_enqueue_style()` in `functions.php` eingebunden — nicht
per `<link>` hardcoded im Template.

### Gutenberg-Unterstützung

Für einfache Seiten (Grundidee, ggf. Über uns) soll der Block Editor verwendbar
sein. `add_theme_support('align-wide')` und minimale `theme.json`-Konfiguration
sichern konsistente Typografie und Farben im Editor.

---

## 10. Plugins — Zusammenfassung

| Plugin | Zweck | Kosten | Alternative |
|--------|-------|--------|-------------|
| **Contact Form 7** | Kontaktformular + Datei-Upload | kostenlos | WPForms Lite |
| **Flamingo** (CF7 Companion) | CF7-Nachrichten im Admin archivieren | kostenlos | — |
| **Advanced Custom Fields** | Koordinaten, Rollen, Custom Fields | kostenlos (Lite) | CMB2 |
| **Leaflet** (direkt, kein Plugin) | Interaktive Karte | kostenlos | — |
| **Yoast SEO** | Meta-Descriptions, Sitemap | kostenlos | RankMath |

**Bewusst nicht verwendet:**
- Elementor / Divi — zu viel Overhead, Entwürfe sind spezifisch genug
- WooCommerce — Shop läuft auf Shopify, wird extern verlinkt
- WPML / Polylang — keine Mehrsprachigkeit geplant

---

## 11. Redaktionelle Pflege

Nach Abschluss der Entwicklung verwaltet der Verein folgende Inhalte im WP-Admin:

| Inhalt | WP-Bereich | Einstieg |
|--------|-----------|----------|
| Neue Beiträge (Kacheln) | Beiträge → Hinzufügen | Titel, Text, Kategorie, Ort, Foto, Koordinaten |
| Teammitglieder | Team → Person hinzufügen | Name, Rolle, Foto, Kurztext |
| Seiteninhalt (Grundidee, Über uns) | Seiten → Bearbeiten | Gutenberg Block Editor |
| Menü | Darstellung → Menüs | Drag & Drop |
| Kontaktformular | Contact → Contact Forms | CF7-Editor |
