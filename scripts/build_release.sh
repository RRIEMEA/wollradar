#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
DEFAULT_OUTPUT_DIR="$(cd "${PROJECT_ROOT}/.." && pwd)/_releases"

RELEASE_NAME="$(date +"%Y-%m-%d_%H%M%S")"
OUTPUT_DIR="${DEFAULT_OUTPUT_DIR}"
APP_NAME="wollradar"
SKIP_ASSETS=0
SKIP_COMPOSER=0

usage() {
    cat <<'EOF'
Usage:
  scripts/build_release.sh [options]

Options:
  --release NAME         Release-Name, z. B. 2026-03-11_01
  --output-dir PATH      Zielordner fuer das Archiv
  --app-name NAME        Praefix fuer den Archivnamen (Standard: wollradar)
  --skip-assets          Kein npm run build ausfuehren
  --skip-composer        Kein composer install --no-dev im Staging ausfuehren
  --help                 Diese Hilfe anzeigen

Das Skript baut ein deploybares Archiv im Stil des STRATO-Runbooks, ohne den
lokalen Working Tree auf Production-Dependencies umzubauen.
EOF
}

require_command() {
    if ! command -v "$1" >/dev/null 2>&1; then
        printf 'Fehlender Befehl: %s\n' "$1" >&2
        exit 1
    fi
}

while [[ $# -gt 0 ]]; do
    case "$1" in
        --release)
            RELEASE_NAME="${2:?Fehlender Wert fuer --release}"
            shift 2
            ;;
        --output-dir)
            OUTPUT_DIR="${2:?Fehlender Wert fuer --output-dir}"
            shift 2
            ;;
        --app-name)
            APP_NAME="${2:?Fehlender Wert fuer --app-name}"
            shift 2
            ;;
        --skip-assets)
            SKIP_ASSETS=1
            shift
            ;;
        --skip-composer)
            SKIP_COMPOSER=1
            shift
            ;;
        --help|-h)
            usage
            exit 0
            ;;
        *)
            printf 'Unbekannte Option: %s\n\n' "$1" >&2
            usage >&2
            exit 1
            ;;
    esac
done

require_command tar
require_command rsync
require_command php

ARCHIVE_PATH="${OUTPUT_DIR}/${APP_NAME}-release-${RELEASE_NAME}.tgz"
STAGING_DIR="$(mktemp -d "${TMPDIR:-/tmp}/${APP_NAME}-release-${RELEASE_NAME}.XXXXXX")"
RSYNC_EXCLUDES=(
    "--exclude=.git/"
    "--exclude=.github/"
    "--exclude=.idea/"
    "--exclude=.vscode/"
    "--exclude=.zed/"
    "--exclude=node_modules/"
    "--exclude=.env"
    "--exclude=.env.*"
    "--exclude=storage/logs/*"
    "--exclude=storage/framework/cache/data/*"
    "--exclude=storage/framework/sessions/*"
    "--exclude=storage/framework/views/*"
    "--exclude=bootstrap/cache/*.php"
    "--exclude=public/uploads/"
    "--exclude=_releases/"
)

cleanup() {
    rm -rf "${STAGING_DIR}"
}

trap cleanup EXIT

printf 'Baue Release %s\n' "${RELEASE_NAME}"

if [[ "${SKIP_ASSETS}" -eq 0 ]]; then
    require_command npm
    printf 'Erzeuge Frontend-Build ...\n'
    (
        cd "${PROJECT_ROOT}"
        npm run build
    )
fi

printf 'Bereite Staging-Verzeichnis vor ...\n'
mkdir -p "${OUTPUT_DIR}"

if [[ "${SKIP_COMPOSER}" -eq 0 ]]; then
    RSYNC_EXCLUDES+=("--exclude=vendor/")
fi

rsync -a "${RSYNC_EXCLUDES[@]}" "${PROJECT_ROOT}/" "${STAGING_DIR}/"

if [[ "${SKIP_COMPOSER}" -eq 0 ]]; then
    require_command composer
    printf 'Installiere Production-Abhaengigkeiten im Staging ...\n'
    composer install \
        --working-dir="${STAGING_DIR}" \
        --no-dev \
        --optimize-autoloader \
        --classmap-authoritative \
        --no-interaction
fi

if [[ -f "${STAGING_DIR}/vendor/autoload.php" ]]; then
    printf 'Leere Laravel-Caches im Staging ...\n'
    (
        cd "${STAGING_DIR}"
        php artisan optimize:clear
    )
else
    printf 'Ueberspringe optimize:clear, weil vendor/autoload.php fehlt.\n'
fi

printf 'Erzeuge Archiv %s ...\n' "${ARCHIVE_PATH}"
(
    cd "${STAGING_DIR}"
    COPYFILE_DISABLE=1 tar -czf "${ARCHIVE_PATH}" .
)

printf 'Release fertig: %s\n' "${ARCHIVE_PATH}"
