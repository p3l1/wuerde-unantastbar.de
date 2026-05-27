# Hero Content & Scroll-Indikator Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Hero-Section der Startseite um Seitentitel, Excerpt, konfigurierbaren Action-Button und animierten Scroll-Indikator ergänzen.

**Architecture:** Alle Inhalte werden direkt in `page-hero.php` gerendert mit bestehenden Lookbook-CSS-Klassen (`demo-hero__content`, `demo-hero__title`, `demo-hero__text`, `demo-hero__actions`). Titel und Excerpt kommen aus WordPress-Bordmitteln. Der Action-Button wird über zwei Custom Fields (`hero_button_text`, `hero_button_url`) konfiguriert, die via `register_meta` in `functions.php` registriert und im Block-Editor unter "Benutzerdefinierte Felder" sichtbar sind. Der Scroll-Indikator ist ein reines CSS+SVG-Element mit bounce-Animation, positioniert absolut mittig unten im Hero.

**Tech Stack:** PHP (WordPress), CSS (bestehende Lookbook-Klassen + neue Animationen), `register_meta` (WordPress Core)

---

## Dateiübersicht

| Datei | Aktion | Zweck |
|---|---|---|
| `theme/page-hero.php` | Ändern | Hero-Content-Markup ergänzen |
| `theme/functions.php` | Ändern | Custom Fields registrieren |
| `theme/style.css` | Ergänzen | Scroll-Indikator-Animation |

---

### Task 1: Custom Fields in functions.php registrieren

**Files:**
- Modify: `theme/functions.php`

`register_meta` macht die Felder im REST API und Block-Editor verfügbar. Ohne Registrierung sind Custom Fields im Block-Editor nicht sichtbar (seit WP 5.5).

- [ ] **Step 1: Custom Fields nach dem `body_class`-Filter ergänzen**

Füge am Ende von `theme/functions.php` (nach dem letzten `add_filter`) ein:

```php
function wuerde_register_hero_meta() {
    $args = [
        'object_subtype' => 'page',
        'type'           => 'string',
        'single'         => true,
        'show_in_rest'   => true,
        'auth_callback'  => function() { return current_user_can( 'edit_posts' ); },
    ];
    register_meta( 'post', 'hero_button_text', $args );
    register_meta( 'post', 'hero_button_url',  $args );
}
add_action( 'init', 'wuerde_register_hero_meta' );
```

- [ ] **Step 2: Im Block-Editor prüfen ob Felder erscheinen**

1. `http://localhost:8080/wp-admin/post.php?post=1619&action=edit` öffnen
2. Rechts oben auf die drei Punkte (⋮) → "Einstellungen" → "Benutzerdefinierte Felder" aktivieren (falls nicht aktiv)
3. Unten in der Editor-Ansicht müssten jetzt die Felder `hero_button_text` und `hero_button_url` erscheinen
4. `hero_button_text` = `Jetzt mitmachen`, `hero_button_url` = `/mach-mit/` eintragen und speichern

- [ ] **Step 3: Committen**

```bash
git add theme/functions.php
git commit -m "feat: register hero_button_text and hero_button_url custom fields"
```

---

### Task 2: Hero-Content in page-hero.php ergänzen

**Files:**
- Modify: `theme/page-hero.php`

Der Hero bekommt:
- `demo-hero__content` Container (unten links, wie im Lookbook)
- `demo-hero__title` mit `get_the_title()`
- `demo-hero__text` mit `get_the_excerpt()` — nur ausgeben wenn nicht leer
- `demo-hero__actions` mit dem Crown-Button — nur ausgeben wenn beide Custom Fields gesetzt sind
- Scroll-Indikator außerhalb von `demo-hero__content`, absolut mittig unten positioniert

Hinweis zur HTML-Struktur: `demo-hero--fullscreen-photo` hat `align-items: flex-end` und `padding-bottom: var(--space-20)`, damit der Content unten landet. Der Scroll-Indikator braucht `position: absolute` damit er unabhängig vom Content-Flow mittig unten sitzt.

- [ ] **Step 1: page-hero.php ersetzen**

```php
<?php
// ABOUTME: Seitentemplate mit Fullscreen-Hero aus dem Beitragsbild.
// ABOUTME: Navbar hängt unter dem Hero und wird beim Hochscrollen sticky.
/**
 * Template Name: Hero
 */

get_header();

$thumbnail_url  = get_the_post_thumbnail_url( get_the_ID(), 'full' );
$hero_style     = $thumbnail_url
    ? ' style="--hero-photo: url(\'' . esc_url( $thumbnail_url ) . '\');"'
    : '';
$button_text    = get_post_meta( get_the_ID(), 'hero_button_text', true );
$button_url     = get_post_meta( get_the_ID(), 'hero_button_url',  true );
$excerpt        = get_the_excerpt();
?>

<section
  class="site-hero demo-hero demo-hero--fullscreen-photo"
  aria-label="<?php echo esc_attr( get_the_title() ); ?>"
  <?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput ?>
>
  <div class="demo-hero__content">

    <h1 class="demo-hero__title"><?php echo esc_html( get_the_title() ); ?></h1>

    <?php if ( $excerpt ) : ?>
      <p class="demo-hero__text"><?php echo esc_html( $excerpt ); ?></p>
    <?php endif; ?>

    <?php if ( $button_text && $button_url ) : ?>
      <div class="demo-hero__actions">
        <a href="<?php echo esc_url( $button_url ); ?>"
           class="btn btn-crown btn--lg">
          <span class="btn-crown__icon" aria-hidden="true"></span>
          <?php echo esc_html( $button_text ); ?>
        </a>
      </div>
    <?php endif; ?>

  </div>

  <div class="site-hero__scroll-indicator" aria-hidden="true">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>

</section>

<?php get_template_part( 'inc/site-header' ); ?>

<main class="page-content" id="main-content" aria-label="Seiteninhalt" tabindex="-1">

  <?php while ( have_posts() ) : the_post(); ?>

    <div class="page-content__entry">
      <?php the_content(); ?>
    </div>

  <?php endwhile; ?>

</main>

<?php get_footer(); ?>
```

- [ ] **Step 2: Committen**

```bash
git add theme/page-hero.php
git commit -m "feat: add title, excerpt, action button and scroll indicator to hero"
```

---

### Task 3: Scroll-Indikator CSS ergänzen

**Files:**
- Modify: `theme/style.css`

Der Indikator braucht:
- `position: absolute; bottom: var(--space-6); left: 50%; transform: translateX(-50%)` — mittig unten
- `z-index: 1` damit er über dem Scrim liegt
- Weiße Farbe mit etwas Transparenz
- `@keyframes hero-bounce` — vertikale bounce-Animation

Der Indikator muss außerhalb von `.demo-hero__content` liegen (das hat seinen eigenen Positioning-Kontext). Da `.demo-hero--fullscreen-photo` `position: relative` durch die `.demo-hero`-Basisklasse hat, funktioniert `position: absolute` korrekt.

- [ ] **Step 1: CSS am Ende des Hero-Template-Abschnitts ergänzen**

Füge nach dem `.site-hero::after`-Block (Ende des Hero-Template-Abschnitts) ein:

```css
/* Scroll-Indikator: mittig unten im Hero, animiert */
.site-hero__scroll-indicator {
  position: absolute;
  bottom: var(--space-8);
  left: 50%;
  transform: translateX(-50%);
  z-index: 2;
  color: rgba(255, 255, 255, 0.7);
  animation: hero-bounce 1.8s ease-in-out infinite;
  pointer-events: none;
}

@keyframes hero-bounce {
  0%, 100% { transform: translateX(-50%) translateY(0); }
  50%       { transform: translateX(-50%) translateY(8px); }
}
```

- [ ] **Step 2: Committen**

```bash
git add theme/style.css
git commit -m "feat: scroll indicator animation for hero template"
```

---

### Task 4: Manuelle Verifikation im Browser

**Files:** keine Änderungen

- [ ] **Step 1: Seitentitel und Excerpt prüfen**

`http://localhost:8080/` öffnen.

Erwartung:
- Seitentitel erscheint als `h1` unten links über dem Foto
- Excerpt erscheint darunter (falls gesetzt — falls nicht, wird kein leeres `<p>` gerendert)

- [ ] **Step 2: Action Button prüfen**

Erwartung: Crown-Button mit dem im Custom Field gesetzten Text und Link erscheint unter dem Excerpt.

Fallback testen: Custom Field `hero_button_text` leeren, Seite speichern, neu laden → kein Button sichtbar.

- [ ] **Step 3: Scroll-Indikator prüfen**

Erwartung: Chevron-Pfeil mittig unten, bounce-Animation läuft.

- [ ] **Step 4: Mobil prüfen** (DevTools 390px)

Erwartung:
- Titel lesbar, nicht abgeschnitten
- Button volle Breite oder umbrechen korrekt
- Scroll-Indikator sichtbar

---

## Self-Review

**Spec-Abgleich:**

| Anforderung | Task |
|---|---|
| Seitentitel im Hero | Task 2 (`get_the_title()` → `demo-hero__title`) |
| Unterschrift im Hero | Task 2 (`get_the_excerpt()` → `demo-hero__text`) |
| Action Button frei konfigurierbar | Task 1 (Custom Fields) + Task 2 (bedingtes Rendering) |
| Button-Konfiguration via WP-UI | Task 1 (`register_meta` → Block-Editor "Benutzerdefinierte Felder") |
| Scroll-Indikator animiert mittig unten | Task 2 (Markup) + Task 3 (CSS bounce) |
| Position unten links | ✅ — `demo-hero--fullscreen-photo` hat `align-items: flex-end` |
| Lookbook-Klassen verwenden | ✅ — `demo-hero__content`, `demo-hero__title`, `demo-hero__text`, `demo-hero__actions` |

**Placeholder-Scan:** Keine TBD/TODO. Alle Schritte haben vollständigen Code.

**Typ-Konsistenz:** `hero_button_text` und `hero_button_url` in Task 1 und Task 2 identisch.
