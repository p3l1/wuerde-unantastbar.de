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

---

## Alle Kartenpins auf der Deutschlandkarte einheitlich türkis

**Entscheidung:** Kartenpins verwenden immer die Markenfarbe Türkis
(`#00aca0`), unabhängig von der zugeordneten Kategorie-Farbe.

**Begründung:** Kundenwunsch — bei Kategorie-Farben je Pin wirkte die Karte
unruhig und die Kategoriezuordnung eines Beitrags war über die Pin-Farbe ohnehin
kaum ablesbar (Popup zeigt Details). Kategoriefarben bleiben auf den
Kacheln/Akkordeon-Punkten bestehen.

---

## Kategorie-Akkordion auf der Mach-mit-Seite startet immer zugeklappt

**Entscheidung:** Beim Laden der Seite sind alle Kategorien zugeklappt; das
Öffnen einer Kategorie klappt automatisch alle anderen wieder zu (exklusives
Akkordeon). Die Live-Suche ist davon ausgenommen — sie öffnet weiterhin alle
Kategorien mit Treffern gleichzeitig, damit Suchergebnisse über mehrere
Kategorien hinweg sichtbar bleiben.

**Begründung:** Kundenwunsch — eine vorab geöffnete Kategorie wirkte
bevorzugt/zufällig und machte die Seite beim ersten Laden unübersichtlich.

---

## Mosaik-Layout auf der Kategorie-Archivseite

**Entscheidung:** Die Kartengrid auf der Kategorie-Archivseite
(`taxonomy-wuerde_kategorie.php`) nutzt CSS Multi-Column-Layout
(`column-count: 2`) statt CSS Grid, wodurch Karten unterschiedlicher Höhe
sich automatisch versetzt anordnen (Pinterest-artiges Mosaik). Auf der
Mach-mit-Seite (Akkordeon-Vorschau) bleibt hingegen das bisherige CSS-Grid mit
gleich hohen Karten bestehen — dort clampen Titel/Text zusätzlich auf max.
2/3 Zeilen für ein einheitliches Kachelbild.

**Begründung:** Kundenwunsch nach zwei unterschiedlichen Darstellungen:
gleich hohe Karten in der Vorschau, versetztes Mosaik auf der vollständigen
Kategorieseite. CSS-Columns erreichen den Mosaik-Effekt nativ, ohne
JavaScript — passend zum bestehenden Architekturprinzip, auf ein
JS-Build-System zu verzichten (siehe `docs/architecture.md`).

---

## Ladeanimation für Bilder: pulsierende gelbe Krone

**Entscheidung:** Solange ein `img[loading="lazy"]`-Bild noch lädt, zeigt ein
generischer Mechanismus in `site.js`/`style.css` an dessen Stelle eine
pulsierende gelbe Krone auf dezentem Hintergrund; nach dem Laden blendet das
Bild sanft ein. Der Mechanismus greift automatisch auf jedes lazy-geladene
Bild im Theme, ohne Änderungen an den einzelnen Templates.

**Begründung:** Kundenwunsch. Ein generischer JS-Durchlauf statt manueller
Markup-Änderungen in den fünf betroffenen Templates vermeidet Redundanz und
erfasst auch künftig neu hinzukommende Bildstellen ohne Zusatzaufwand.

---

## Mitmach-Einreichungsformular: Felder in drei Abschnitte gruppiert

**Entscheidung:** Das Einreichungsformular (`mitmach-einreichung`-Block) ist in
drei benannte Abschnitte gegliedert — „Dein Beitrag" (Titel, Kategorie,
Beschreibung, Kurzbeschreibung), „Ort" (Adresse, Ort, Karte) und „Deine
Kontaktdaten" (Name, E-Mail, Telefon). Die Zustimmungs-Checkboxen zur
Veröffentlichung von E-Mail/Telefon stehen jetzt direkt beim jeweiligen Feld
statt gesammelt am Ende. Der Karten-Toggle folgt direkt auf Adresse/Ort statt
danach isoliert zu stehen.

**Begründung:** Die bisherige Reihenfolge (Name, E-Mail, Titel, Beschreibung,
Kurzbeschreibung, Kategorie, Adresse/Ort, Telefon, Zustimmungen, Karte) mischte
Identität, Inhalt und Ort ohne erkennbare Struktur; insbesondere lag die
E-Mail-Zustimmung sieben Felder von der E-Mail-Adresse entfernt. Inhalt zuerst
folgt dem Muster öffentlicher Einreichungsformulare (niedrigschwellige,
inhaltliche Felder zuerst, Kontaktdaten zuletzt) und passt zum
Seitenschwerpunkt „Ideen und Umsetzungsbeispiele teilen" aus `PROJECT.md`.
