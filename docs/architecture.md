# Architektur-Entscheidungen

Technische Architekturentscheidungen für die wuerde-unantastbar.de Website.

---

## WordPress als CMS

**Entscheidung:** WordPress wird als Content-Management-System eingesetzt.

**Begründung:** Der Verein verfügt über kein technisches Personal. WordPress ermöglicht es,
Inhalte eigenständig zu pflegen, ohne Entwicklerkenntnisse vorauszusetzen. Die große
Plugin-Ökosystem deckt alle benötigten Funktionen (Formulare, Karten, Suche) ab.

---

## Lokale Entwicklungsumgebung mit Docker

**Entscheidung:** Die lokale Entwicklung läuft über Docker Compose.

**Begründung:** Stellt sicher, dass alle Entwickler identische Umgebungen nutzen und
verhindert Abhängigkeitskonflikte mit dem Host-System. Das Setup ist in
`docker-compose.yml` und `Makefile` dokumentiert.

---

## Lookbook als PHP-Template

**Entscheidung:** Das Lookbook wird als WordPress Page Template (`page-lookbook.php`)
ohne Build-Step implementiert.

**Begründung:** Da das Theme bewusst kein JavaScript-Build-System (webpack, Vite o.ä.)
einsetzt, werden alle Lookbook-Komponenten direkt als PHP/HTML gerendert. Dies hält
die Abhängigkeiten minimal und macht das Lookbook wartbar, ohne dass Entwickler eine
Node.js-Toolchain einrichten müssen. Eigene CSS Custom Properties aus `style.css`
sorgen für konsistentes Styling ohne Framework. Assets (`lookbook.css`, `lookbook.js`)
werden per `wp_enqueue_scripts` nur auf Lookbook-Seiten geladen (`is_page_template`).

---

## Verlaufs-Funktion als gemeinsame Quelle für Backend und Frontend

**Entscheidung:** `theme/inc/kategorie-gradient.php` erzeugt den CSS-Verlauf der
Kategorie-Banner. Sowohl die Vorschau-Miniaturen der Einstellungsseite
(`theme/inc/settings-darstellung.php`) als auch der Banner in
`theme/single-wuerde_beitrag.php` rufen dieselbe Funktion
`wuerde_kategorie_gradient( array $colors, ?string $variant = null )` auf.

**Begründung:** Eine zweite Implementierung nur für die Vorschau würde über die Zeit
vom echten Banner abweichen — die Redaktion würde etwas auswählen, das die Website
anders darstellt. Der Verlauf wird als CSS Custom Property `--cat-gradient` ins
`style`-Attribut geschrieben und in `style.css` als `background-image` gelesen; die
bestehende `background-color` bleibt als Fallback stehen. Liefert die Funktion einen
leeren String (keine Kategorie oder ein nicht auflösbarer Farbwert), greift dieser
Fallback automatisch.

**Einstellungsseite:** Eine eigene Seite (`wuerde-darstellung`) statt eines weiteren
Abschnitts der Formular-Einstellungen — die beiden Seiten haben getrennte Zielgruppen
(Technik/Zugangsdaten gegenüber Gestaltung). Sie hängt per `add_submenu_page` unter
*Mitmach-Beiträge* statt unter *Einstellungen*, weil sie ausschließlich Mitmach-Beiträge
betrifft und dort gesucht wird. Gespeichert wird weiter über die Options API; außerhalb
der Einstellungen-Seiten druckt WordPress die Speichern-Meldung nicht selbst, deshalb
ruft die Seite `settings_errors()` explizit auf. Das Admin-CSS wird über
`load-{$hook_suffix}` nur auf dieser einen Seite eingebunden und nicht in `style.css`
abgelegt, das ausschließlich das Frontend versorgt.
