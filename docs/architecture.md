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
