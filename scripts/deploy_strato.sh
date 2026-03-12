#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
BUILD_SCRIPT="${SCRIPT_DIR}/build_release.sh"

COMMAND="deploy"
RELEASE_NAME=""
ARCHIVE_PATH=""
CONFIG_FILE=""
DRY_RUN=0
EXTRA_BUILD_ARGS=()
SSH_EXTRA_ARGS=()

usage() {
    cat <<'EOF'
Usage:
  scripts/deploy_strato.sh [command] [options]

Commands:
  build      Release-Archiv lokal bauen
  upload     Archiv nach STRATO hochladen
  prepare    deploy_release.sh auf dem Server ausfuehren
  switch     deploy_switch.sh auf dem Server ausfuehren
  status     deploy_status.sh auf dem Server ausfuehren
  deploy     build + upload + prepare + switch + status

Options:
  --release NAME         Release-Name, z. B. 2026-03-11_01
  --archive PATH         Vorhandenes Archiv verwenden
  --config PATH          Alternative Konfigurationsdatei laden
  --skip-assets          An build_release.sh weiterreichen
  --skip-composer        An build_release.sh weiterreichen
  --dry-run              Nur anzeigen, nichts ausfuehren
  --help                 Diese Hilfe anzeigen

Konfiguration:
  Das Skript laedt optional .deploy.env im Projektroot oder eine Datei aus
  --config. Siehe scripts/deploy.env.example.
EOF
}

require_command() {
    if ! command -v "$1" >/dev/null 2>&1; then
        printf 'Fehlender Befehl: %s\n' "$1" >&2
        exit 1
    fi
}

run_local() {
    if [[ "${DRY_RUN}" -eq 1 ]]; then
        printf '[dry-run] %s\n' "$*"
        return
    fi

    "$@"
}

run_remote() {
    local remote_command="$1"

    if [[ "${DRY_RUN}" -eq 1 ]]; then
        printf '[dry-run] '
        printf '%q ' ssh "${SSH_EXTRA_ARGS[@]}" -p "${STRATO_DEPLOY_PORT}" \
            "${STRATO_DEPLOY_USER}@${STRATO_DEPLOY_HOST}" \
            "${remote_command}"
        printf '\n'
        return
    fi

    ssh "${SSH_EXTRA_ARGS[@]}" -p "${STRATO_DEPLOY_PORT}" \
        "${STRATO_DEPLOY_USER}@${STRATO_DEPLOY_HOST}" \
        "${remote_command}"
}

run_upload() {
    local upload_target
    upload_target="${STRATO_REMOTE_UPLOAD_TARGET:-${STRATO_REMOTE_UPLOAD_DIR}}"

    if [[ "${DRY_RUN}" -eq 1 ]]; then
        printf '[dry-run] '
        printf '%q ' scp "${SSH_EXTRA_ARGS[@]}" -P "${STRATO_DEPLOY_PORT}" \
            "${ARCHIVE_PATH}" \
            "${STRATO_DEPLOY_USER}@${STRATO_DEPLOY_HOST}:${upload_target}/"
        printf '\n'
        return
    fi

    scp "${SSH_EXTRA_ARGS[@]}" -P "${STRATO_DEPLOY_PORT}" \
        "${ARCHIVE_PATH}" \
        "${STRATO_DEPLOY_USER}@${STRATO_DEPLOY_HOST}:${upload_target}/"
}

load_config() {
    local candidate="${PROJECT_ROOT}/.deploy.env"

    if [[ -n "${CONFIG_FILE}" ]]; then
        candidate="${CONFIG_FILE}"
    fi

    if [[ -f "${candidate}" ]]; then
        # shellcheck disable=SC1090
        source "${candidate}"
    fi
}

ensure_remote_config() {
    : "${STRATO_DEPLOY_USER:?Bitte STRATO_DEPLOY_USER in .deploy.env setzen.}"
    : "${STRATO_DEPLOY_HOST:?Bitte STRATO_DEPLOY_HOST in .deploy.env setzen.}"

    STRATO_DEPLOY_PORT="${STRATO_DEPLOY_PORT:-22}"
    STRATO_APP_ROOT="${STRATO_APP_ROOT:-/home/${STRATO_DEPLOY_USER}/apps/wollradar}"
    STRATO_REMOTE_UPLOAD_DIR="${STRATO_REMOTE_UPLOAD_DIR:-${STRATO_APP_ROOT}/incoming_upload}"
    STRATO_REMOTE_PREPARE_SCRIPT="${STRATO_REMOTE_PREPARE_SCRIPT:-${STRATO_APP_ROOT}/deploy_release.sh}"
    STRATO_REMOTE_SWITCH_SCRIPT="${STRATO_REMOTE_SWITCH_SCRIPT:-${STRATO_APP_ROOT}/deploy_switch.sh}"
    STRATO_REMOTE_STATUS_SCRIPT="${STRATO_REMOTE_STATUS_SCRIPT:-${STRATO_APP_ROOT}/deploy_status.sh}"
    STRATO_SSH_KEY="${STRATO_SSH_KEY:-}"
    STRATO_SSH_EXTRA_OPTS="${STRATO_SSH_EXTRA_OPTS:-}"

    SSH_EXTRA_ARGS=()

    if [[ -n "${STRATO_SSH_KEY}" ]]; then
        SSH_EXTRA_ARGS+=(-i "${STRATO_SSH_KEY}")
    fi

    if [[ -n "${STRATO_SSH_EXTRA_OPTS}" ]]; then
        # Auf einfache, shell-ueblich quotierte Optionen beschraenkt.
        # shellcheck disable=SC2206
        local extra_opts=( ${STRATO_SSH_EXTRA_OPTS} )
        SSH_EXTRA_ARGS+=("${extra_opts[@]}")
    fi
}

ensure_release_name() {
    if [[ -z "${RELEASE_NAME}" ]]; then
        RELEASE_NAME="$(date +"%Y-%m-%d_%H%M%S")"
    fi
}

infer_release_from_archive() {
    if [[ -n "${RELEASE_NAME}" || -z "${ARCHIVE_PATH}" ]]; then
        return
    fi

    local archive_name prefix suffix
    archive_name="$(basename "${ARCHIVE_PATH}")"
    prefix="${DEPLOY_APP_NAME:-wollradar}-release-"
    suffix=".tgz"

    if [[ "${archive_name}" == "${prefix}"*"${suffix}" ]]; then
        RELEASE_NAME="${archive_name#"${prefix}"}"
        RELEASE_NAME="${RELEASE_NAME%"${suffix}"}"
    fi
}

build_archive() {
    ensure_release_name
    local app_name output_dir
    app_name="${DEPLOY_APP_NAME:-wollradar}"
    output_dir="${RELEASE_OUTPUT_DIR:-$(cd "${PROJECT_ROOT}/.." && pwd)/_releases}"

    if [[ ${#EXTRA_BUILD_ARGS[@]} -gt 0 ]]; then
        run_local "${BUILD_SCRIPT}" \
            --release "${RELEASE_NAME}" \
            --app-name "${app_name}" \
            --output-dir "${output_dir}" \
            "${EXTRA_BUILD_ARGS[@]}"
    else
        run_local "${BUILD_SCRIPT}" \
            --release "${RELEASE_NAME}" \
            --app-name "${app_name}" \
            --output-dir "${output_dir}"
    fi

    if [[ -z "${ARCHIVE_PATH}" ]]; then
        ARCHIVE_PATH="${output_dir}/${app_name}-release-${RELEASE_NAME}.tgz"
    fi
}

prepare_release() {
    local remote_archive
    remote_archive="${STRATO_REMOTE_UPLOAD_DIR}/$(basename "${ARCHIVE_PATH}")"
    run_remote "\"${STRATO_REMOTE_PREPARE_SCRIPT}\" \"${remote_archive}\" \"${RELEASE_NAME}\""
}

switch_release() {
    run_remote "\"${STRATO_REMOTE_SWITCH_SCRIPT}\" \"${RELEASE_NAME}\""
}

status_release() {
    run_remote "\"${STRATO_REMOTE_STATUS_SCRIPT}\""
}

if [[ $# -gt 0 ]]; then
    case "$1" in
        build|upload|prepare|switch|status|deploy)
            COMMAND="$1"
            shift
            ;;
    esac
fi

while [[ $# -gt 0 ]]; do
    case "$1" in
        --release)
            RELEASE_NAME="${2:?Fehlender Wert fuer --release}"
            shift 2
            ;;
        --archive)
            ARCHIVE_PATH="${2:?Fehlender Wert fuer --archive}"
            shift 2
            ;;
        --config)
            CONFIG_FILE="${2:?Fehlender Wert fuer --config}"
            shift 2
            ;;
        --skip-assets)
            EXTRA_BUILD_ARGS+=("--skip-assets")
            shift
            ;;
        --skip-composer)
            EXTRA_BUILD_ARGS+=("--skip-composer")
            shift
            ;;
        --dry-run)
            DRY_RUN=1
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

load_config
infer_release_from_archive

case "${COMMAND}" in
    build)
        build_archive
        printf 'Archiv: %s\n' "${ARCHIVE_PATH}"
        ;;
    upload)
        ensure_remote_config
        if [[ -z "${ARCHIVE_PATH}" ]]; then
            build_archive
        fi
        require_command scp
        run_upload
        ;;
    prepare)
        ensure_remote_config
        ensure_release_name
        : "${ARCHIVE_PATH:?Bitte --archive angeben oder zuerst build/upload ausfuehren.}"
        require_command ssh
        prepare_release
        ;;
    switch)
        ensure_remote_config
        ensure_release_name
        require_command ssh
        switch_release
        ;;
    status)
        ensure_remote_config
        require_command ssh
        status_release
        ;;
    deploy)
        ensure_remote_config
        require_command ssh
        require_command scp
        if [[ -z "${ARCHIVE_PATH}" ]]; then
            build_archive
        fi
        run_upload
        prepare_release
        switch_release
        status_release
        ;;
esac
