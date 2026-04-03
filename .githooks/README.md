# Git Hooks

Dieses Verzeichnis enthält Git Hooks für das Projekt.

## Aktivierung

Nach dem Klonen des Repos einmalig ausführen:

```bash
git config core.hooksPath .githooks
chmod +x .githooks/pre-push
```

Oder über das Makefile (falls vorhanden):

```bash
make setup
```

## Hooks

### pre-push

Verhindert direktes Pushen auf `main` oder `master`.
Alle Änderungen müssen über einen Pull Request eingereicht werden.
