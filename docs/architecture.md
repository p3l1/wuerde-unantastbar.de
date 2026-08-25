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

## Lookbook aus dem Theme entfernt

**Entscheidung:** Die Demo-Templates (`page-lookbook.php`, `page-hero-demo.php`,
`lookbook.js`, `_preview.html`) wurden samt zugehörigem CSS aus dem Theme entfernt.

**Begründung:** Der Release-Workflow zippt das komplette `theme/`-Verzeichnis als
Produktions-Artefakt. Damit wären die Demo-Templates (inkl. Platzhalter-URLs zu
placehold.co und als „Template Name" im Editor auswählbar) auf dem Live-Server
gelandet. Das Lookbook existierte als Entwicklungshilfe ohne Build-Step; wird es
wieder gebraucht, lässt es sich aus der Git-Historie (< v0.8) wiederherstellen.

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

---

## Externe Dienste nur mit Einwilligung (DSGVO)

**Entscheidung:** Das Frontend lädt ohne Nutzer-Einwilligung keine Ressourcen von
Drittservern. Konkret: Schriften (Pally, Tanker) liegen lokal in `assets/fonts/`
statt beim Fontshare-CDN; OpenStreetMap-Kacheln und die Nominatim-Adresssuche
werden über ein Click-to-Load-Gate (`assets/osm-consent.js`, localStorage-Merker)
erst nach Bestätigung geladen; WP-Emoji ist deaktiviert (lud Grafiken von s.w.org).
Einzige Ausnahme ist hCaptcha als Spam-Schutz der Formulare.

**Begründung:** Jeder Request an einen Drittserver überträgt die Besucher-IP und
ist nach DSGVO einwilligungspflichtig (vgl. LG München zu Google Fonts). Für einen
Verein mit Menschenrechts-Fokus ist Datensparsamkeit zudem Glaubwürdigkeitsfrage.
Die Einwilligung wird bewusst clientseitig in localStorage gemerkt — kein Cookie,
kein Server-Tracking, kein Consent-Banner-Plugin nötig.
