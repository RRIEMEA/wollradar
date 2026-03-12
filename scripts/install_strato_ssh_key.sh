#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"

CONFIG_FILE=""
SSH_EXTRA_ARGS=()

usage() {
    cat <<'EOF'
Usage:
  scripts/install_strato_ssh_key.sh [--config PATH]

Liest .deploy.env, verbindet sich einmal per Passwort mit STRATO und
hinterlegt den konfigurierten Public Key in ~/.ssh/authorized_keys.
EOF
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

build_ssh_args() {
    : "${STRATO_DEPLOY_USER:?Bitte STRATO_DEPLOY_USER in .deploy.env setzen.}"
    : "${STRATO_DEPLOY_HOST:?Bitte STRATO_DEPLOY_HOST in .deploy.env setzen.}"

    STRATO_DEPLOY_PORT="${STRATO_DEPLOY_PORT:-22}"
    STRATO_SSH_KEY="${STRATO_SSH_KEY:-$HOME/.ssh/id_ed25519}"
    STRATO_SSH_EXTRA_OPTS="${STRATO_SSH_EXTRA_OPTS:-}"

    SSH_EXTRA_ARGS=()

    if [[ -n "${STRATO_SSH_EXTRA_OPTS}" ]]; then
        # shellcheck disable=SC2206
        local extra_opts=( ${STRATO_SSH_EXTRA_OPTS} )
        SSH_EXTRA_ARGS+=("${extra_opts[@]}")
    fi
}

while [[ $# -gt 0 ]]; do
    case "$1" in
        --config)
            CONFIG_FILE="${2:?Fehlender Wert fuer --config}"
            shift 2
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
build_ssh_args

PUBLIC_KEY_PATH="${STRATO_SSH_KEY}.pub"

if [[ ! -f "${PUBLIC_KEY_PATH}" ]]; then
    printf 'Public Key nicht gefunden: %s\n' "${PUBLIC_KEY_PATH}" >&2
    exit 1
fi

printf 'Installiere %s auf %s@%s:%s\n' \
    "${PUBLIC_KEY_PATH}" \
    "${STRATO_DEPLOY_USER}" \
    "${STRATO_DEPLOY_HOST}" \
    "${STRATO_DEPLOY_PORT}"

ssh "${SSH_EXTRA_ARGS[@]}" -p "${STRATO_DEPLOY_PORT}" \
    "${STRATO_DEPLOY_USER}@${STRATO_DEPLOY_HOST}" \
    'umask 077; mkdir -p ~/.ssh; touch ~/.ssh/authorized_keys; chmod 700 ~/.ssh; chmod 600 ~/.ssh/authorized_keys'

ssh "${SSH_EXTRA_ARGS[@]}" -p "${STRATO_DEPLOY_PORT}" \
    "${STRATO_DEPLOY_USER}@${STRATO_DEPLOY_HOST}" \
    'key="$(cat)"; grep -qxF "$key" ~/.ssh/authorized_keys || printf "%s\n" "$key" >> ~/.ssh/authorized_keys' \
    < "${PUBLIC_KEY_PATH}"

printf 'Pruefe passwortlosen Login ...\n'
ssh "${SSH_EXTRA_ARGS[@]}" -o BatchMode=yes -p "${STRATO_DEPLOY_PORT}" \
    "${STRATO_DEPLOY_USER}@${STRATO_DEPLOY_HOST}" \
    'printf "SSH-Key-Login OK\n"'

