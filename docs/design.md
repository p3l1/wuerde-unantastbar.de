# Design-Entscheidungen

Gestalterische Entscheidungen und Begründungen für die wuerde-unantastbar.de Website.

---

## Primärfarben

**Entscheidung:** Zwei Primärfarben: Gelb (`#f7bc2f`) und Türkis (`#00aca0`).

**Begründung:** Die Farben stammen vom bestehenden Vereinslogo und sind Teil der
etablierten Markenidentität. Sie vermitteln Wärme (Gelb) und Verlässlichkeit (Türkis)
und werden als CSS custom properties im Theme hinterlegt:

```css
:root {
  --color-primary:   #f7bc2f;
  --color-secondary: #00aca0;
}
```

---

## Startseite nicht im Hauptmenü

**Entscheidung:** Die Startseite erscheint nicht als Menüpunkt — der Rückweg erfolgt
über das Logo.

**Begründung:** Vorgabe aus dem Kundenbriefing (`PROJECT.md`). Vereinfacht die
Navigation und hebt „Mach mit" als wichtigsten Einstiegspunkt hervor.
