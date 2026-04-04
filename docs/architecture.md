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
