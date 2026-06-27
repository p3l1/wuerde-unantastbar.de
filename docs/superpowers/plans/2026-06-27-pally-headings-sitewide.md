# Pally Headings Sitewide — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** All h1–h6 heading elements use Pally (`--font-brand`) sitewide; Tanker is retained as `--font-tanker` for future use.

**Architecture:** Two-file change — `style.css` (CSS token rename + 12 font-family swaps) and `theme.json` (font slug rename + h1/h2/h3 preset update). No new files. No logic changes.

**Tech Stack:** WordPress block theme, CSS custom properties, theme.json v3.

## Global Constraints

- Branch: `8-pally-alle-ueberschriften` (already created)
- Conventional commits required
- No backwards-compat shims — rename the token, update every callsite
- Body font (`--font-body`), buttons, and navigation must not change
- No new font downloads — Pally already loaded via Fontshare `@import`

---

### Task 1: Rename `--font-headline` → `--font-tanker` and swap all heading font references in `style.css`

**Files:**
- Modify: `theme/style.css`

**Interfaces:**
- Produces: CSS token `--font-tanker` (Tanker), all heading selectors now use `var(--font-brand)` (Pally)

- [ ] **Step 1: Rename the token in `:root`**

In `theme/style.css` find the `:root` block (around line 56–62):

```css
  /* ==========================================================================
     Typografie
     Schriften: PALLY (Vereinsname/Headlines), TANKER (Subheadlines)
     Quelle: https://www.fontshare.com
     ========================================================================== */

  --font-brand:    'Pally', sans-serif;    /* Logo, Hauptüberschriften */
  --font-headline: 'Tanker', sans-serif;   /* Headlines, Zwischenüberschriften */
```

Replace with:

```css
  /* ==========================================================================
     Typografie
     Schriften: PALLY (Überschriften/Vereinsname), TANKER (reserviert)
     Quelle: https://www.fontshare.com
     ========================================================================== */

  --font-brand:   'Pally', sans-serif;   /* Überschriften, Vereinsname */
  --font-tanker:  'Tanker', sans-serif;  /* Reserviert für künftige explizite Nutzung */
```

- [ ] **Step 2: Replace all 12 `var(--font-headline)` → `var(--font-brand)` in one pass**

Using the Edit tool with `replace_all: true`, replace every occurrence of `var(--font-headline)` with `var(--font-brand)` in `theme/style.css`. This covers all 12 callsites:

| Selector | ~Line |
|---|---|
| `h1, h2, h3, h4, h5, h6` | 182 |
| `.lb-section__title` | 335 |
| `.lb-type-row span` | 628 |
| `.card__title` | 1052 |
| `.highlight-box__title` | 1104 |
| `.demo-hero__title` | 1441 |
| `.demo-subpage-hero__title` | 1508 |
| `.mitmach-card__title` | 1879 |
| `.profile-card__name` | 2037 |
| `.hero-demo-content__inner h2` | 2450 |
| `.grundidee-banner .highlight-box__title.grundidee-banner__title` | 3047 |
| `.page-header-section__title` | 3068 |

- [ ] **Step 3: Verify no `--font-headline` remains**

```bash
grep -n 'font-headline' theme/style.css
```

Expected output: empty (no matches).

- [ ] **Step 4: Commit**

```bash
git add theme/style.css
git commit -m "feat: use Pally for all headings, retain Tanker as --font-tanker

Renames --font-headline to --font-tanker and replaces all heading
font references with var(--font-brand) (Pally).

Closes #8"
```

---

### Task 2: Update `theme.json` — rename Tanker slug and update h1/h2/h3 preset

**Files:**
- Modify: `theme/theme.json`

**Interfaces:**
- Produces: WP preset `--wp--preset--font-family--tanker` for Tanker; h1/h2/h3 use `--wp--preset--font-family--brand`

- [ ] **Step 1: Rename the Tanker font family entry**

In `theme/theme.json`, `settings.typography.fontFamilies`, find:

```json
        {
          "slug": "headline",
          "name": "Tanker (Headlines)",
          "fontFamily": "'Tanker', sans-serif"
        },
```

Replace with:

```json
        {
          "slug": "tanker",
          "name": "Tanker",
          "fontFamily": "'Tanker', sans-serif"
        },
```

- [ ] **Step 2: Update h1, h2, h3 to use the `brand` preset**

In `theme/theme.json`, `styles.elements`, find the three heading entries (h1, h2, h3). Each has:

```json
          "fontFamily": "var(--wp--preset--font-family--headline)"
```

Replace all three with:

```json
          "fontFamily": "var(--wp--preset--font-family--brand)"
```

(Use `replace_all: true` on the Edit tool — the string is identical in all three blocks.)

- [ ] **Step 3: Verify no `--headline` preset remains in theme.json**

```bash
grep 'headline' theme/theme.json
```

Expected output: empty (no matches).

- [ ] **Step 4: Commit**

```bash
git add theme/theme.json
git commit -m "feat(theme.json): rename Tanker slug to 'tanker', use Pally for headings"
```

---

### Task 3: Post GitHub issue comment and open PR

**Files:** none (GitHub CLI only)

- [ ] **Step 1: Post TODO comment on issue #8**

```bash
gh issue comment 8 --repo p3l1/wuerde-unantastbar.de --body "TODO: Klären, wo Tanker (\`--font-tanker\`) künftig explizit eingesetzt werden soll — z. B. für Subheadlines, Eyebrows oder Zitate."
```

- [ ] **Step 2: Push branch**

```bash
git push -u origin 8-pally-alle-ueberschriften
```

- [ ] **Step 3: Open PR**

```bash
gh pr create \
  --title "feat: Pally für alle Überschriften sitewide (#8)" \
  --body "$(cat <<'EOF'
## Zusammenfassung

- Benennt CSS-Token `--font-headline` um in `--font-tanker` (Tanker bleibt erhalten, aber ohne Zuweisung)
- Ersetzt alle 12 Verwendungen von `var(--font-headline)` durch `var(--font-brand)` (Pally) in `style.css`
- Aktualisiert `theme.json`: Tanker-Slug `headline` → `tanker`, h1/h2/h3 verwenden jetzt `--wp--preset--font-family--brand`

## Testplan

- [ ] WordPress-Seite öffnen: alle Überschriften erscheinen in Pally
- [ ] Startseite Hero-h1 erscheint ebenfalls in Pally
- [ ] Unterseiten-Page-Header erscheinen in Pally
- [ ] Fließtext, Buttons und Navigation sind **unverändert** in System-/Body-Schrift
- [ ] Gutenberg-Editor: Font-Picker zeigt „Tanker" (nicht mehr „Tanker (Headlines)")
- [ ] `grep 'font-headline' theme/style.css theme/theme.json` liefert keine Treffer

🤖 Generated with [Claude Code](https://claude.com/claude-code)
EOF
)"
```
