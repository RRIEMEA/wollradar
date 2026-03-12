# Wollradar

Wollradar ist eine Laravel-12-WebApp zum Verwalten von Garnen, Projekten und Stammdaten. Die aktuelle UI ist mobile-first aufgebaut und als installierbare WebApp nutzbar.

## Lokal starten

Voraussetzungen:

- PHP 8.2+
- Composer
- Node.js + npm
- optional Mailpit fuer lokale Testmails

Setup:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
```

Entwicklung:

```bash
php artisan serve
npm run dev
```

App lokal oeffnen:

- [http://127.0.0.1:8000](http://127.0.0.1:8000)

## Lokale Mails

Die lokale `.env` ist auf Mailpit ausgelegt:

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

Mailpit-Weboberflaeche:

- [http://127.0.0.1:8025](http://127.0.0.1:8025)

## STRATO-Deploy

Der Deploy-Ablauf folgt dem vorhandenen STRATO-Runbook. Voraussetzung ist, dass auf dem Server bereits diese Skripte existieren:

- `~/apps/wollradar/deploy_release.sh`
- `~/apps/wollradar/deploy_switch.sh`
- `~/apps/wollradar/deploy_status.sh`

Zusatzannahmen:

- `~/apps/wollradar/shared/.env` und `~/apps/wollradar/shared/storage` werden serverseitig verwendet
- `current/public` wird serverseitig nach `~/htdocs/wollradar_web` gespiegelt
- der Webroot bootet dauerhaft ueber `~/apps/wollradar/live`, damit STRATO kein altes Release unter dem Pfad `current` im Opcode-Cache festhaelt
- Composer wird nicht auf STRATO ausgefuehrt; `vendor/` kommt aus dem lokalen Release-Artefakt

### 1. Konfiguration anlegen

```bash
cp scripts/deploy.env.example .deploy.env
```

Dann in `.deploy.env` mindestens `STRATO_DEPLOY_USER` und `STRATO_DEPLOY_HOST` setzen.

Hinweis:

- `STRATO_REMOTE_UPLOAD_DIR` ist der serverseitige echte Pfad fuer `deploy_release.sh`
- `STRATO_REMOTE_UPLOAD_TARGET` ist das Ziel fuer `scp`; auf manchen STRATO-Setups muss das `~/incoming_upload` sein
- `STRATO_WEB_ROOT` ist der echte Live-Webroot, z. B. `.../htdocs/wollradar_web`
- `STRATO_WEB_APP_SYMLINK` ist optional und standardmaessig `live`
- `STRATO_SSH_KEY` kann auf den lokalen Deploy-Key zeigen, z. B. `~/.ssh/id_ed25519`
- `STRATO_SSH_EXTRA_OPTS="-o UpdateHostKeys=no"` unterdrueckt die bekannte STRATO-Warnung zur RSA-Hostkey-Aktualisierung

Optional kannst Du den Public Key einmalig auf den STRATO-User kopieren:

```bash
scripts/install_strato_ssh_key.sh
```

Dabei wird einmal das STRATO-Passwort abgefragt. Danach sollte `deploy_strato.sh`
ohne Passwort-Prompt durchlaufen.

### 2. Release bauen

```bash
scripts/build_release.sh --release 2026-03-11_01
```

Das Skript:

- baut `public/build` lokal mit Vite
- erstellt ein sauberes Staging-Verzeichnis
- installiert dort Production-PHP-Dependencies
- leert Laravel-Caches
- erzeugt ein `.tgz` unter `../_releases`

### 3. Vollstaendig deployen

```bash
scripts/deploy_strato.sh deploy --release 2026-03-11_01
```

Dieser Befehl:

- baut das Release-Archiv
- laedt es per `scp` nach STRATO hoch
- ruft serverseitig `deploy_release.sh` auf
- schaltet mit `deploy_switch.sh` um
- zeigt zum Schluss `deploy_status.sh`

Wenn fuer den STRATO-User bereits ein Public Key in `~/.ssh/authorized_keys` hinterlegt ist,
laeuft der Deploy damit ohne Passwort-Prompt durch.

### Einzelne Schritte

Nur Upload:

```bash
scripts/deploy_strato.sh upload --archive /Users/rainerrichter/VsLocal/_releases/wollradar-release-2026-03-11_01.tgz
```

Nur Release vorbereiten:

```bash
scripts/deploy_strato.sh prepare --release 2026-03-11_01 --archive /Users/rainerrichter/VsLocal/_releases/wollradar-release-2026-03-11_01.tgz
```

Nur umschalten:

```bash
scripts/deploy_strato.sh switch --release 2026-03-11_01
```

Nur Status:

```bash
scripts/deploy_strato.sh status
```

Trockentest ohne echte Verbindung:

```bash
scripts/deploy_strato.sh deploy --release 2026-03-11_01 --dry-run
```

## Hinweise

- Deploy-Caches aus `bootstrap/cache/*.php` werden nicht ins Archiv aufgenommen.
- `.env`, Logs und Runtime-Dateien aus `storage/` werden nicht versioniert oder deployed.
- Fuer mobile/PWA-Tests ist `localhost` oder echtes `https` empfehlenswert.
