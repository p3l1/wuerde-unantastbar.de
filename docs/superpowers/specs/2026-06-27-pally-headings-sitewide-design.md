# Design: Pally für alle Überschriften (Issue #8)

**Status:** Approved  
**Datum:** 2026-06-27

## Ziel

Alle h1–h6-Elemente auf der Website sollen die Vereinsschrift **Pally** (`--font-brand`) verwenden. Ausnahmen gibt es keine — auch der Fullscreen-Hero der Startseite wechselt zu Pally.

Tanker bleibt als CSS-Token `--font-tanker` erhalten, wird aber vorerst keiner Überschriften-Ebene zugewiesen.

## Änderungen

### 1. `theme/style.css` — Token umbenennen

In `:root`:
- `--font-headline: 'Tanker', sans-serif` → `--font-tanker: 'Tanker', sans-serif`
- Kommentarblock anpassen

### 2. `theme/style.css` — Alle 12 Font-Verwendungen ersetzen

Alle `var(--font-headline)` → `var(--font-brand)`:

| Klasse / Selektor | Zeile |
|---|---|
| `h1, h2, h3, h4, h5, h6` (global) | 182 |
| `.lb-section__title` | 335 |
| `.lb-type-row span` | 628 |
| `.card__title` | 1052 |
| `.highlight-box__title` | 1104 |
| `.demo-hero__title` | 1441 |
| `.demo-subpage-hero__title` | 1508 |
| `.mitmach-card__title` | 1879 |
| `.profile-card__name` | 2037 |
| `.hero-demo-content__inner h2` | 2450 |
| `.grundidee-banner__title` | 3047 |
| `.page-header-section__title` | 3068 |

### 3. `theme/theme.json`

- Font-Family-Eintrag: slug `headline` → `tanker`, Name `"Tanker (Headlines)"` → `"Tanker"`
- h1, h2, h3: `--wp--preset--font-family--headline` → `--wp--preset--font-family--brand`

### 4. GitHub-Issue-Kommentar

TODO-Notiz posten: Klären, wo Tanker (`--font-tanker`) künftig eingesetzt werden soll.

## Nicht im Scope

- Texte, Buttons, Navigation — bleiben unverändert
- Kein neuer Font-Download — Pally wird bereits über Fontshare geladen
- Keine Änderungen an Block-CSS-Dateien (keine Font-Referenzen vorhanden)
