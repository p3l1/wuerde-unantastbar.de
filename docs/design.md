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

## Keine Farbverläufe

**Entscheidung:** Farbverläufe (Gradients) werden nicht verwendet.

**Begründung:** Die Gestaltung setzt auf klare, flächige Farben. Verläufe widersprechen
dem sachlichen und zugänglichen Charakter des Vereinsauftritts.

---

## Startseite nicht im Hauptmenü

**Entscheidung:** Die Startseite erscheint nicht als Menüpunkt — der Rückweg erfolgt
über das Logo.

**Begründung:** Vorgabe aus dem Kundenbriefing (`PROJECT.md`). Vereinfacht die
Navigation und hebt „Mach mit" als wichtigsten Einstiegspunkt hervor.

---

## Overscroll-Farben auf Mobilgeräten

**Entscheidung:** Der Bereich außerhalb der Seite (Browser-Chrome und
Overscroll-Bounce auf iOS) wird eingefärbt: oben Gelb, unten Türkis. Ist nur
eine Farbe möglich (Browser-Chrome via `theme-color`), gilt Gelb.

**Begründung:** Der `theme-color`-Meta-Tag unterstützt nur eine Farbe und färbt
Statusleiste/Toolbar in Markengelb. Der Zwei-Farben-Effekt (oben Gelb, unten
Türkis) entsteht über einen fixierten Verlauf am `html`-Element, den der opake
`body` verdeckt — sichtbar nur beim Über-den-Rand-Ziehen. Dies ist ein
technischer Kniff für die Systembereiche, kein sichtbarer Design-Verlauf, und
steht daher nicht im Widerspruch zu „Keine Farbverläufe".
