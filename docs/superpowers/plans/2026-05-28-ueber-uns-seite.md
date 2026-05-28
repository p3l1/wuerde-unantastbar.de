# „Über uns"-Seite Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Die „Über uns"-Seite des Vereins mit einem Custom Gutenberg-Block für Personen-Vorstellungen ausstatten, der alle Inhalte über die WordPress-UI bearbeitbar macht.

**Architecture:** Ein neuer Custom Block `wuerde/team-grid` rendert Profil-Karten aus den vorhandenen `.profile-card`-CSS-Klassen. Personendaten werden als Custom Post Type `wuerde_person` gespeichert — so kann der Redakteur Personen anlegen, Fotos hochladen und Texte pflegen, ohne Code anzufassen. Der Block wählt aus, welche Personen angezeigt werden, und rendert sie auf dem Frontend mit PHP. Die „Über uns"-Seite verwendet das bestehende Hero-Template und nutzt danach den neuen Block zusammen mit regulären Gutenberg-Blöcken.

**Tech Stack:** WordPress Custom Post Type, Custom Gutenberg Block (apiVersion 3, render.php, editor.js via @wordpress/scripts-freier Ansatz wie die vorhandenen Blöcke), PHP, CSS Custom Properties aus `style.css`

---

## Dateistruktur

| Aktion    | Datei                               | Verantwortung                                     |
| --------- | ----------------------------------- | ------------------------------------------------- |
| Erstellen | `theme/blocks/team-grid/block.json` | Block-Metadaten und Attribute                     |
| Erstellen | `theme/blocks/team-grid/render.php` | Frontend-Rendering der Profil-Karten              |
| Erstellen | `theme/blocks/team-grid/editor.js`  | Block-Editor UI (InspectorControls)               |
| Erstellen | `theme/blocks/team-grid/editor.css` | Minimal-Stil im Block Editor                      |
| Ändern    | `theme/inc/blocks.php`              | Neuen Block registrieren                          |
| Erstellen | `theme/inc/cpt-person.php`          | CPT `wuerde_person` registrieren + Meta speichern |
| Ändern    | `theme/functions.php`               | `cpt-person.php` einbinden                        |

---

## Task 1: Custom Post Type `wuerde_person`

**Files:**
- Create: `theme/inc/cpt-person.php`
- Modify: `theme/functions.php`

- [ ] **Schritt 1: `cpt-person.php` anlegen**

```php
<?php
// ABOUTME: Custom Post Type für Vereinsmitglieder der "Über uns"-Seite.
// ABOUTME: Speichert Name, Rolle, Jahrgang, Kurzbiografie und verknüpft das Portrait als Featured Image.

function wuerde_register_person_cpt() {
    register_post_type( 'wuerde_person', [
        'labels'       => [
            'name'               => 'Personen',
            'singular_name'      => 'Person',
            'add_new_item'       => 'Neue Person hinzufügen',
            'edit_item'          => 'Person bearbeiten',
            'search_items'       => 'Personen suchen',
            'not_found'          => 'Keine Personen gefunden.',
            'not_found_in_trash' => 'Keine Personen im Papierkorb.',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => true,
        'supports'     => [ 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes' ],
        'menu_icon'    => 'dashicons-groups',
        'rewrite'      => false,
        'has_archive'  => false,
    ] );
}
add_action( 'init', 'wuerde_register_person_cpt' );

function wuerde_register_person_meta() {
    $args = [
        'object_subtype' => 'wuerde_person',
        'type'           => 'string',
        'single'         => true,
        'show_in_rest'   => true,
        'auth_callback'  => function() { return current_user_can( 'edit_posts' ); },
    ];
    register_meta( 'post', 'person_role',      $args );
    register_meta( 'post', 'person_birthyear', $args );
    register_meta( 'post', 'person_button_url', $args );
}
add_action( 'init', 'wuerde_register_person_meta' );

function wuerde_person_meta_box() {
    add_meta_box(
        'wuerde_person_fields',
        'Personen-Details',
        'wuerde_person_meta_box_html',
        'wuerde_person',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wuerde_person_meta_box' );

function wuerde_person_meta_box_html( WP_Post $post ) {
    wp_nonce_field( 'wuerde_person_meta', 'wuerde_person_nonce' );
    $fields = [
        'person_role'       => [ 'label' => 'Rolle / Berufe',  'type' => 'text', 'placeholder' => 'z.B. Tischler · Theologe · Diakon' ],
        'person_birthyear'  => [ 'label' => 'Jahrgang',        'type' => 'text', 'placeholder' => 'z.B. 1964' ],
        'person_button_url' => [ 'label' => 'Button-URL',       'type' => 'url',  'placeholder' => 'https://' ],
    ];
    echo '<table class="form-table" role="presentation"><tbody>';
    foreach ( $fields as $key => $field ) {
        $value = esc_attr( get_post_meta( $post->ID, $key, true ) );
        $ph    = esc_attr( $field['placeholder'] );
        echo "<tr><th scope='row'><label for='{$key}'>{$field['label']}</label></th>";
        echo "<td><input type='{$field['type']}' id='{$key}' name='{$key}' value='{$value}' placeholder='{$ph}' class='regular-text'></td></tr>";
    }
    echo '</tbody></table>';
    echo '<p class="description">Kurzbiografie: im Textbereich oben eingeben. Portrait: als Beitragsbild (rechte Sidebar) hochladen.</p>';
}

function wuerde_save_person_meta( int $post_id ) {
    if ( ! isset( $_POST['wuerde_person_nonce'] ) ) return;
    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wuerde_person_nonce'] ) ), 'wuerde_person_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $keys = [ 'person_role', 'person_birthyear', 'person_button_url' ];
    foreach ( $keys as $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
        }
    }
}
add_action( 'save_post_wuerde_person', 'wuerde_save_person_meta' );
```

- [ ] **Schritt 2: `functions.php` einbinden**

In `theme/functions.php` nach der Zeile `require_once get_template_directory() . '/inc/cpt.php';` einfügen:

```php
require_once get_template_directory() . '/inc/cpt-person.php';
```

- [ ] **Schritt 3: WordPress-Admin aufrufen und prüfen**

Im WP-Admin (http://localhost:8080/wp-admin) erscheint in der linken Sidebar der neue Menüpunkt „Personen" mit dem Gruppen-Icon. Test: eine Beispielperson anlegen, Jahrgang und Rolle eingeben, Beitragsbild hochladen. Nach dem Speichern sollten die Werte erhalten bleiben.

- [ ] **Schritt 4: Commit**

```bash
git add theme/inc/cpt-person.php theme/functions.php
git commit -m "feat: add wuerde_person custom post type with meta fields"
```

---

## Task 2: Custom Block `wuerde/team-grid` — Grundgerüst

**Files:**
- Create: `theme/blocks/team-grid/block.json`
- Create: `theme/blocks/team-grid/editor.js`
- Create: `theme/blocks/team-grid/editor.css`
- Modify: `theme/inc/blocks.php`

- [ ] **Schritt 1: `block.json` anlegen**

```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "wuerde/team-grid",
  "title": "Team-Raster",
  "category": "wuerde",
  "description": "Zeigt Vereinsmitglieder als Profil-Karten aus dem CPT wuerde_person.",
  "keywords": ["team", "personen", "profil", "über uns"],
  "textdomain": "wuerde-unantastbar",
  "attributes": {
    "layout": {
      "type": "string",
      "default": "horizontal",
      "enum": ["horizontal", "vertical"]
    },
    "showButton": {
      "type": "boolean",
      "default": false
    },
    "buttonLabel": {
      "type": "string",
      "default": "Mehr erfahren"
    },
    "selectedIds": {
      "type": "array",
      "items": { "type": "number" },
      "default": []
    }
  },
  "supports": {
    "html": false,
    "align": false
  },
  "render": "file:./render.php",
  "editorScript": "file:./editor.js",
  "editorStyle": "file:./editor.css"
}
```

- [ ] **Schritt 2: `editor.js` anlegen**

```js
// ABOUTME: Block-Editor-UI für den Team-Raster-Block.
// ABOUTME: Zeigt eine Vorschau der Personen-Auswahl und Layout-Einstellungen im Inspector.
( function () {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, SelectControl, ToggleControl, TextControl, CheckboxControl, Placeholder } = wp.components;
    const { useSelect } = wp.data;
    const { __ } = wp.i18n;

    registerBlockType( 'wuerde/team-grid', {
        edit( { attributes, setAttributes } ) {
            const { layout, showButton, buttonLabel, selectedIds } = attributes;
            const blockProps = useBlockProps();

            const persons = useSelect( ( select ) => {
                return select( 'core' ).getEntityRecords( 'postType', 'wuerde_person', {
                    per_page: 100,
                    status: 'publish',
                    orderby: 'menu_order',
                    order: 'asc',
                } ) ?? [];
            }, [] );

            const togglePerson = ( id, checked ) => {
                if ( checked ) {
                    setAttributes( { selectedIds: [ ...selectedIds, id ] } );
                } else {
                    setAttributes( { selectedIds: selectedIds.filter( ( i ) => i !== id ) } );
                }
            };

            return (
                wp.element.createElement( 'div', blockProps,
                    wp.element.createElement( InspectorControls, null,
                        wp.element.createElement( PanelBody, { title: __( 'Layout', 'wuerde-unantastbar' ), initialOpen: true },
                            wp.element.createElement( SelectControl, {
                                label: __( 'Karten-Stil', 'wuerde-unantastbar' ),
                                value: layout,
                                options: [
                                    { label: 'Horizontal (breit)', value: 'horizontal' },
                                    { label: 'Vertikal (kompakt)', value: 'vertical' },
                                ],
                                onChange: ( val ) => setAttributes( { layout: val } ),
                            } )
                        ),
                        wp.element.createElement( PanelBody, { title: __( 'Button', 'wuerde-unantastbar' ), initialOpen: false },
                            wp.element.createElement( ToggleControl, {
                                label: __( 'Button anzeigen', 'wuerde-unantastbar' ),
                                checked: showButton,
                                onChange: ( val ) => setAttributes( { showButton: val } ),
                            } ),
                            showButton && wp.element.createElement( TextControl, {
                                label: __( 'Button-Beschriftung', 'wuerde-unantastbar' ),
                                value: buttonLabel,
                                onChange: ( val ) => setAttributes( { buttonLabel: val } ),
                            } )
                        ),
                        wp.element.createElement( PanelBody, { title: __( 'Personen', 'wuerde-unantastbar' ), initialOpen: true },
                            persons.length === 0
                                ? wp.element.createElement( 'p', null, __( 'Keine veröffentlichten Personen gefunden.', 'wuerde-unantastbar' ) )
                                : persons.map( ( person ) =>
                                    wp.element.createElement( CheckboxControl, {
                                        key: person.id,
                                        label: person.title.rendered,
                                        checked: selectedIds.includes( person.id ),
                                        onChange: ( checked ) => togglePerson( person.id, checked ),
                                    } )
                                )
                        )
                    ),
                    wp.element.createElement( Placeholder, {
                        icon: 'groups',
                        label: __( 'Team-Raster', 'wuerde-unantastbar' ),
                        instructions: selectedIds.length === 0
                            ? __( 'Wähle im rechten Panel Personen aus.', 'wuerde-unantastbar' )
                            : selectedIds.length + __( ' Person(en) ausgewählt — Vorschau auf der Seite.', 'wuerde-unantastbar' ),
                    } )
                )
            );
        },
        save: () => null,
    } );
} )();
```

- [ ] **Schritt 3: `editor.css` anlegen**

```css
/* ABOUTME: Minimal-Stil für den Team-Raster-Block im Block Editor. */
.wp-block-wuerde-team-grid .components-placeholder {
    min-height: 120px;
}
```

- [ ] **Schritt 4: Block in `theme/inc/blocks.php` registrieren**

In der Funktion `wuerde_register_blocks()` den neuen Eintrag hinzufügen:

```php
foreach ( [ 'mitmach-list', 'mitmach-map', 'team-grid' ] as $slug ) {
    register_block_type( get_template_directory() . '/blocks/' . $slug );
}
```

(Ersetze nur die `foreach`-Zeile — der Rest der Funktion bleibt unverändert.)

- [ ] **Schritt 5: Im Block-Editor prüfen**

Auf einer Testseite in WP-Admin den Block „Team-Raster" (Kategorie „Würde unantastbar") einfügen. Der Inspector zeigt Layout-Auswahl und Personen-Checkboxes. Kein JS-Fehler in der Browserkonsole.

- [ ] **Schritt 6: Commit**

```bash
git add theme/blocks/team-grid/ theme/inc/blocks.php
git commit -m "feat: add team-grid block skeleton with editor UI"
```

---

## Task 3: Frontend-Rendering (`render.php`)

**Files:**
- Create: `theme/blocks/team-grid/render.php`

- [ ] **Schritt 1: `render.php` anlegen**

```php
<?php
// ABOUTME: Frontend-Rendering des Team-Raster-Blocks.
// ABOUTME: Gibt Profil-Karten für ausgewählte wuerde_person-Posts aus.

$layout      = sanitize_key( $attributes['layout'] ?? 'horizontal' );
$show_button = (bool) ( $attributes['showButton'] ?? false );
$button_lbl  = esc_html( $attributes['buttonLabel'] ?? 'Mehr erfahren' );
$selected    = array_map( 'intval', (array) ( $attributes['selectedIds'] ?? [] ) );

$query_args = [
    'post_type'      => 'wuerde_person',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
];

if ( ! empty( $selected ) ) {
    $query_args['post__in'] = $selected;
    $query_args['orderby']  = 'post__in';
}

$persons = get_posts( $query_args );

if ( empty( $persons ) ) {
    return;
}

$card_class = 'horizontal' === $layout ? 'profile-card' : 'profile-card profile-card--vertical';
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'team-grid' ] ); ?>>
<?php foreach ( $persons as $person ) :
    $photo_url  = get_the_post_thumbnail_url( $person->ID, 'large' );
    $role       = esc_html( get_post_meta( $person->ID, 'person_role', true ) );
    $birthyear  = esc_html( get_post_meta( $person->ID, 'person_birthyear', true ) );
    $btn_url    = esc_url( get_post_meta( $person->ID, 'person_button_url', true ) );
    $bio        = wp_kses_post( wpautop( $person->post_content ) );

    $media_style = $photo_url
        ? 'style="--profile-photo: url(\'' . esc_url( $photo_url ) . '\');"'
        : '';
    $media_extra = $photo_url ? '' : ' profile-card__media--solid-teal';
    $no_photo    = $photo_url ? '' : ' profile-card--no-photo';
?>
    <article class="<?php echo esc_attr( $card_class . $no_photo ); ?>">
        <div class="profile-card__media<?php echo esc_attr( $media_extra ); ?>"
             <?php echo $media_style; // phpcs:ignore WordPress.Security.EscapeOutput ?>
             aria-hidden="true">
            <?php if ( ! $photo_url ) : ?>
                <span class="crown-watermark" aria-hidden="true"></span>
            <?php endif; ?>
        </div>
        <div class="profile-card__body">
            <?php if ( $birthyear ) : ?>
                <p class="profile-card__meta">
                    <span class="crown-accent" aria-hidden="true"></span>
                    Jahrgang <?php echo $birthyear; ?>
                </p>
            <?php endif; ?>
            <h3 class="profile-card__name"><?php echo esc_html( $person->post_title ); ?></h3>
            <?php if ( $role ) : ?>
                <p class="profile-card__title"><?php echo $role; ?></p>
            <?php endif; ?>
            <?php if ( $bio ) : ?>
                <div class="profile-card__text"><?php echo $bio; ?></div>
            <?php endif; ?>
            <?php if ( $show_button && $btn_url ) : ?>
                <div class="profile-card__actions">
                    <a class="btn btn--outline" href="<?php echo $btn_url; ?>"><?php echo $button_lbl; ?></a>
                </div>
            <?php endif; ?>
        </div>
    </article>
<?php endforeach; ?>
</div>
```

- [ ] **Schritt 2: CSS für `.team-grid` in `style.css` ergänzen**

Am Ende des Abschnitts `PROFIL-KARTEN` (nach Zeile ~2083) in `theme/style.css` anfügen:

```css
/* ==========================================================================
   TEAM-RASTER (Über-uns-Seite)
   ========================================================================== */

.team-grid {
  display: flex;
  flex-direction: column;
  gap: var(--space-6);
}

.team-grid:has(.profile-card--vertical) {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: var(--space-6);
}
```

- [ ] **Schritt 3: Auf der Seite testen**

Eine Testseite mit dem Team-Raster-Block und 2–3 ausgewählten Personen auf dem Frontend aufrufen. Die Karten sollen mit dem korrekten Foto, Namen, Jahrgang, Rolle und Bio erscheinen.

- [ ] **Schritt 4: Commit**

```bash
git add theme/blocks/team-grid/render.php theme/style.css
git commit -m "feat: add team-grid block frontend rendering and CSS"
```

---

## Task 4: „Über uns"-Seite aufbauen

**Files:**
- Keine Code-Änderungen — reine WordPress-Konfiguration via WP-Admin

- [ ] **Schritt 1: Personen anlegen**

Unter `Personen > Neue Person hinzufügen` für jedes Vorstandsmitglied einen Eintrag erstellen:

| Name                 | Jahrgang | Rolle                                                       | Bio-Quelle    | Foto-URL (upload oder import)                                                  |
| -------------------- | -------- | ----------------------------------------------------------- | ------------- | ------------------------------------------------------------------------------ |
| Ralf Knoblauch       | 1964     | Tischler · Dipl. Theologe · Diakon · Bildhauer              | Lookbook-Text | https://wuerde-unantastbar.de/wp-content/uploads/2024/02/LS_02405-scaled.jpg   |
| Anja Knoblauch       | 1970     | Dipl. Theologin · Pastoralreferentin                        | Lookbook-Text | https://wuerde-unantastbar.de/wp-content/uploads/2024/02/LS_02379-scaled.jpg   |
| Lukas Schmalenstroer | 1993     | Sozialarbeiter · Jugendreferent · Fotograf · Berufungscoach | Lookbook-Text | https://wuerde-unantastbar.de/wp-content/uploads/2024/02/LS_02481-683x1024.jpg |
| Christoph Henn       | 1952     | Dipl. Agr. Ing. · Sonderschullehrer & Heilpädagoge (i.R.)   | Lookbook-Text | https://wuerde-unantastbar.de/wp-content/uploads/2024/11/LS_06793-683x1024.jpg |

Texte aus dem Lookbook (`page-lookbook.php` Zeilen 484, 498, 512, 526):
- **Ralf:** „Die unantastbare Würde des Menschen ist seit vielen Jahren mein Lebensthema. Es findet Ausdruck in Königsskulpturen aus Eichenholz — und genau daraus entstand der Impuls für diese Initiative."
- **Anja:** „Mit meinem Engagement möchte ich einen nachhaltigen Beitrag zu einer offenen, vielfältigen und solidarischen Gesellschaft leisten — und sichtbare Zeichen für demokratische, menschenzugewandte Haltungen stärken."
- **Lukas:** „Für Vielfalt und Toleranz einzustehen ist nicht optional. Ich mache bei „für Menschenwürde und Demokratie" mit, weil dieser Ansatz positiv ist: eher FÜR etwas zu stehen, als dagegen."
- **Christoph:** „Ich erlebe, dass Menschlichkeit und gegenseitige Akzeptanz alles andere als selbstverständlich sind. Deshalb engagiere ich mich dafür, dass Menschen unabhängig von Herkunft oder Orientierung in wertschätzendem Miteinander leben können."

Reihenfolge über „Reihenfolge" (menu_order) im Seitenattribute-Panel setzen (1–4).

- [ ] **Schritt 2: „Über uns"-Seite in WP-Admin konfigurieren**

Unter `Seiten` die „Über uns"-Seite aufrufen (oder neu erstellen).

Seitentemplate: **Hero** (für den Unterseiten-Header mit Beitragsbild).

Beitragsbild: ein passendes Gruppenfoto hochladen.

Hero-Felder (im Meta-Box „Hero-Einstellungen"):
- Titel: `Über uns`
- Untertitel: `Das Team hinter dem Verein für Menschenwürde und Demokratie e.V.`
- Button-Text: (leer lassen)

- [ ] **Schritt 3: Seiten-Inhalt im Block-Editor aufbauen**

Folgende Block-Struktur im Editor aufbauen:

```
[Überschrift H2] — „Wer wir sind"
[Absatz] — kurze Einleitung (aus der bestehenden wuerde-unantastbar.de-Seite: „Wir sind ein gemeinnütziger Verein …")
[Team-Raster Block] — alle 4 Personen ausgewählt, Layout: Horizontal
[Absatz / Trennlinie]
[Blockzitat] — „Die Würde des Menschen ist unantastbar." (Art. 1 GG)
```

- [ ] **Schritt 4: Mobilansicht prüfen**

Browser-DevTools auf 375px Breite umschalten. Karten sollen untereinander gestapelt sein und das Foto oben erscheinen (responsive Regel in style.css greift automatisch über `grid-template-columns: 1fr` im mobilen Breakpoint).

Mobile Responsive-Regel für Profil-Karten prüfen — sie steht bereits in `style.css` (Bereich `@media (max-width: 767px)`). Falls `profile-card` dort nicht auf `grid-template-columns: 1fr` gesetzt ist, muss das ergänzt werden (prüfen und ggf. in Schritt 5 nachbessern).

- [ ] **Schritt 5: Mobile-Responsive prüfen und ggf. ergänzen**

```bash
grep -n "profile-card" /Users/philipp/github/wuerde-unantastbar.de/theme/style.css | grep -A5 "767px\|480px"
```

Falls die Profil-Karten im mobilen Breakpoint noch kein `grid-template-columns: 1fr` haben, am Ende des `@media (max-width: 767px)`-Blocks in `style.css` einfügen:

```css
.profile-card,
.profile-card--vertical {
  grid-template-columns: 1fr;
}

.profile-card__media {
  min-height: 200px;
}
```

---

## Task 5: Abschluss und Qualitätssicherung

**Files:**
- Ggf. `theme/style.css` (mobile-responsive Nachbesserung aus Task 4)

- [ ] **Schritt 1: Responsive prüfen (Breakpoints 375px, 768px, 1280px)**

Seite „Über uns" in allen drei Breakpoints im Browser aufrufen. Karten sollen:
- 375px: einspaltig, Foto oben
- 768px: einspaltig (horizontal) oder 2-spaltig (vertikal)
- 1280px: volle Breite

- [ ] **Schritt 2: WP-Admin-Bearbeitbarkeit prüfen**

Im WP-Admin eine Person bearbeiten (Jahrgang ändern), Seite speichern, Frontend neu laden — Änderung muss sichtbar sein. Block-Inspector: Personen-Checkbox hinzufügen/entfernen, Layout umschalten — beides muss funktionieren.

- [ ] **Schritt 3: Kein Inline-PHP in Inhalten**

Alle Inhalte kommen aus dem CPT und dem Block. Nichts ist hartkodiert. Prüfen: `grep -r "Ralf Knoblauch" theme/blocks/` → keine Treffer.

- [ ] **Schritt 4: Abschluss-Commit**

```bash
git add theme/
git commit -m "feat: complete ueber-uns page with team-grid block and person CPT"
```

---

## Spec-Abgleich

| Anforderung                                                   | Abgedeckt durch                                                |
| ------------------------------------------------------------- | -------------------------------------------------------------- |
| Personen-Vorstellung über vorhandene Profil-Karten-Komponente | Task 3: render.php nutzt `.profile-card`-CSS                   |
| Alle Inhalte über WP-UI bearbeitbar                           | Task 1: CPT + Meta-Felder; Task 4: WP-Admin-Seite              |
| Custom Felder für Personen                                    | Task 1: `person_role`, `person_birthyear`, `person_button_url` |
| Custom Block im Block-Editor wählbar                          | Task 2+3: `wuerde/team-grid` in Kategorie „Würde unantastbar"  |
| Unterseiten-Header (wie PROJECT.md beschrieben)               | Task 4: Hero-Template                                          |
| Stimmiges Gesamtbild mit bestehenden Texten                   | Task 4: Texte aus Lookbook übernehmen                          |
| Mobilfreundlichkeit                                           | Task 4+5: responsive prüfen und ggf. ergänzen                  |
