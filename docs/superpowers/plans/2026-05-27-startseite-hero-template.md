# Startseite Hero-Template Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ein WordPress-Seitentemplate "Hero" erstellen, das das Beitragsbild der Seite als Fullscreen-Hero (100vw/100vh, `object-fit: cover`) anzeigt, mit transparent schwebendem Header, und anschließend den normalen Block-Editor-Inhalt ausgibt.

**Architecture:** Neues Template `page-hero.php` — analog zu `page.php`, aber mit einem `.site-hero`-Block vor dem Content, der das Beitragsbild via `get_the_post_thumbnail_url()` als CSS-Variable einbindet. Die CSS-Klassen kommen direkt aus dem bestehenden Lookbook (`demo-hero--fullscreen-photo`, `demo-header--transparent`). Der transparente Header-Modus wird über eine `body`-Klasse aktiviert, die in `site.js` keinen weiteren Eingriff erfordert — das CSS regelt alles. Das Template erscheint in der WordPress-Seitenbearbeitung im Dropdown "Seitenattribute → Template".

**Tech Stack:** PHP (WordPress Page Template), CSS (bestehende Lookbook-Klassen + neue Site-Klassen), WordPress `post-thumbnails` Support (bereits aktiv in `functions.php`)

---

## Dateiübersicht

| Datei                 | Aktion    | Zweck                                                         |
| --------------------- | --------- | ------------------------------------------------------------- |
| `theme/page-hero.php` | Erstellen | Das neue Seitentemplate                                       |
| `theme/style.css`     | Ergänzen  | CSS für `.site-hero` und transparenten Header im Hero-Kontext |

---

### Task 1: Seitentemplate `page-hero.php` anlegen

**Files:**
- Create: `theme/page-hero.php`

Das Template muss:
1. Den `site-header` transparent schalten (via Body-Klasse)
2. Das Beitragsbild als Fullscreen-Hero vor dem Content zeigen
3. Fallback auf ein neutrales Dunkel (`#000`) wenn kein Beitragsbild gesetzt ist
4. Den normalen Block-Editor-Inhalt danach ausgeben (`the_content()`)
5. In der WP-UI als "Hero" erscheinen

- [ ] **Step 1: Template-Datei erstellen**

```php
<?php
// ABOUTME: Seitentemplate mit Fullscreen-Hero aus dem Beitragsbild.
// ABOUTME: Transparenter Header schwebt über dem Foto; danach normaler Seiteninhalt.
/**
 * Template Name: Hero
 */

get_header();

$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
$hero_style    = $thumbnail_url
    ? ' style="--hero-photo: url(\'' . esc_url( $thumbnail_url ) . '\');"'
    : '';
?>

<section
  class="site-hero demo-hero demo-hero--fullscreen-photo"
  aria-label="<?php echo esc_attr( get_the_title() ); ?>"
  <?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput ?>
></section>

<main class="page-content" id="main-content" aria-label="Seiteninhalt" tabindex="-1">

  <?php while ( have_posts() ) : the_post(); ?>

    <div class="page-content__entry">
      <?php the_content(); ?>
    </div>

  <?php endwhile; ?>

</main>

<?php get_footer(); ?>
```

- [ ] **Step 2: Datei committen**

```bash
git add theme/page-hero.php
git commit -m "feat: add Hero page template scaffold"
```

---

### Task 2: Body-Klasse für Hero-Template setzen

**Files:**
- Modify: `theme/functions.php`

Der transparente Header wird über eine Body-Klasse gesteuert, sodass kein JavaScript und keine Änderung an `header.php` nötig ist.

- [ ] **Step 1: Body-Klassen-Filter in `functions.php` ergänzen**

Füge nach dem bestehenden `add_action( 'wp_enqueue_scripts', ... )` Block ein:

```php
function wuerde_body_classes( $classes ) {
    if ( is_page_template( 'page-hero.php' ) ) {
        $classes[] = 'has-hero-template';
    }
    return $classes;
}
add_filter( 'body_class', 'wuerde_body_classes' );
```

- [ ] **Step 2: Committen**

```bash
git add theme/functions.php
git commit -m "feat: add body class for hero template"
```

---

### Task 3: CSS für transparenten Header und Hero

**Files:**
- Modify: `theme/style.css`

Drei Dinge müssen als CSS ergänzt werden:

1. `.has-hero-template .site-header` — macht den echten `site-header` transparent und `position: absolute` (wie `.demo-header--transparent` im Lookbook)
2. `.has-hero-template .site-content` — entfernt das Standard-`padding-top` des `.site-content`, damit der Hero direkt unter dem transparenten Header beginnt
3. `.site-hero` — damit das Element die korrekte Höhe hat (wird von `.demo-hero--fullscreen-photo` auf `min-height: 100vh` gesetzt, aber die explizite `width: 100vw` und `height: 100vh` + kein Padding soll sichergestellt werden)

Hintergrund: Das Lookbook hat `demo-hero--fullscreen-photo` mit `min-height: 100vh`. Wir wollen exakt `100vh` (nicht mehr), also wird `min-height` auf `100vh` belassen, aber `height: 100vh` explizit gesetzt und `max-height: 100vh` zur Sicherheit ergänzt.

Das `.site-content` in `footer.php` schließt den Wrapper — der `<div class="site-content">` wird in `header.php` geöffnet. Das Hero-Section liegt aber *vor* `.page-content` und damit noch innerhalb von `.site-content`. Ein negatives `margin-top` auf `.site-content` ist nicht nötig — der Header ist `position: sticky`, also nimmt er normalen Fluss-Platz ein. Bei `position: absolute` (transparenter Header) fällt er aus dem Fluss, und das Hero beginnt bei `top: 0`.

- [ ] **Step 1: CSS am Ende des Abschnitts "SITE CHROME" ergänzen**

Suche den Block ab Zeile ~2468 (`.site-header { position: sticky; ... }`). Füge **nach** dem `.site-content`-Block (derzeit letzte Zeile ~2673) ein:

```css
/* ==========================================================================
   HERO-TEMPLATE — Transparenter Header über Vollbild-Foto
   ========================================================================== */

/* Site-Header wird transparent und schwebt über dem Hero */
.has-hero-template .site-header {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  z-index: 200;
  background: transparent;
  border-bottom: none;
}

/* Markenname und Nav-Links auf weiss */
.has-hero-template .site-header .site-header__brand {
  color: var(--color-white);
}

.has-hero-template .site-header .site-nav__link {
  color: rgba(255, 255, 255, 0.85);
}

.has-hero-template .site-header .site-nav__link:hover,
.has-hero-template .site-header .site-nav__link--active {
  background: rgba(255, 255, 255, 0.12);
  color: var(--color-white);
}

/* Hamburger-Linien weiss */
.has-hero-template .site-header .site-hamburger span {
  background: var(--color-white);
}

/* site-content braucht keinen eigenen top-offset, da Header aus dem Fluss fällt */
.has-hero-template .site-content {
  padding-top: 0;
}

/* Hero-Section: exakt Viewport-Höhe, keine eigene Padding */
.site-hero {
  height: 100vh;
  max-height: 100vh;
  padding: 0;
}
```

- [ ] **Step 2: Committen**

```bash
git add theme/style.css
git commit -m "feat: transparent header and fullscreen hero styles for hero template"
```

---

### Task 4: Manuelle Verifikation im Browser

**Files:** keine Änderungen

- [ ] **Step 1: WordPress lokal starten**

```bash
docker compose up -d
```

Erwartete Ausgabe: Container starten ohne Fehler.

- [ ] **Step 2: In WordPress-Admin das Template zuweisen**

1. `http://localhost:8080/wp-admin` öffnen
2. Seiten → Startseite öffnen
3. Rechts im Block-Editor unter "Seitenattribute" → "Template": **Hero** auswählen
4. Beitragsbild der Seite setzen (falls noch nicht gesetzt): rechte Seitenleiste → "Beitragsbild" → Bild hochladen oder auswählen
5. Speichern

- [ ] **Step 3: Frontend prüfen**

`http://localhost:8080/` aufrufen und prüfen:

- Hero-Foto füllt den gesamten Viewport (100vw × 100vh), kein weißer Rand oben
- Header schwebt transparent über dem Foto (weiße Schrift, kein weißer Hintergrund)
- Beim Scrollen: Navbar bleibt am oberen Rand sticky (nach dem Hero-Bereich)
- Unterhalb des Hero: normaler Seiteninhalt (`page-content__entry`)
- Kein sichtbares Beitragsbild-Element im Content-Bereich (WordPress fügt es nicht automatisch ein — `the_content()` gibt nur Blöcke aus)

- [ ] **Step 4: Ohne Beitragsbild testen**

Beitragsbild der Seite entfernen, Seite neu laden.

Erwartung: Hero zeigt schwarzen Hintergrund (`--color-black` via `.demo-hero` Basisstil), kein JavaScript-Fehler in der Konsole.

- [ ] **Step 5: Mobil prüfen** (DevTools → responsive Mode, z.B. 390px Breite)

- Hero füllt mobil ebenfalls den Viewport
- Hamburger-Menü sichtbar und weiß
- Mobile Nav öffnet sich korrekt

---

## Self-Review

**Spec-Abgleich:**

| Anforderung                                | Task                                                                         |
| ------------------------------------------ | ---------------------------------------------------------------------------- |
| Seitentemplate "Hero"                      | Task 1                                                                       |
| Beitragsbild automatisch als Hero-Foto     | Task 1 (get_the_post_thumbnail_url)                                          |
| Fullscreen 100vw/100vh, cover              | Task 1 (CSS-Klasse `demo-hero--fullscreen-photo`) + Task 3 (`height: 100vh`) |
| Normaler Seiteninhalt danach               | Task 1 (the_content() nach der Hero-Section)                                 |
| Konfiguration nur über WordPress-UI        | Task 2+3 (Template-Dropdown + Beitragsbild)                                  |
| Kein Eingriff in semantischen Seiteninhalt | ✅ — nur Template-Auswahl, Content unberührt                                 |
| Transparenter Header über Foto             | Task 2 (Body-Klasse) + Task 3 (CSS)                                          |
| Existing Lookbook-Elemente nutzen          | ✅ — `demo-hero--fullscreen-photo` direkt verwendet                          |

**Placeholder-Scan:** Keine TBD/TODO gefunden. Alle Schritte haben vollständigen Code.

**Typ-Konsistenz:** `page-hero.php` und `is_page_template( 'page-hero.php' )` stimmen überein.
