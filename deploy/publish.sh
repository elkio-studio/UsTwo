#!/usr/bin/env bash
set -euo pipefail

fail() { printf '%s\n' "$1" >&2; exit 1; }

[[ "${GITHUB_REF:-}" == refs/heads/main && "${GITHUB_SHA:-}" =~ ^[a-f0-9]{40}$ ]] \
    || fail 'Solo se puede publicar un commit de main.'
[[ "$(git rev-parse HEAD)" == "$GITHUB_SHA" ]] || fail 'El checkout no coincide con el commit de CI.'

service="${RENDER_SERVICE_ID:-}"
if [[ -z "$service" ]]; then
    [[ "${RENDER_BOOTSTRAP:-}" == true ]] || fail 'Falta configurar el servicio Render.'
else
    [[ "$service" =~ ^srv-[a-z0-9]+$ && -n "${RENDER_API_KEY:-}" \
        && "${RENDER_PUBLIC_URL:-}" =~ ^https://[a-z0-9-]+\.onrender\.com/?$ ]] \
        || fail 'Configuración de Render incompleta.'
fi

[[ -n "${SUPABASE_MIGRATION_URL:-}" && -n "${SUPABASE_CA_PEM:-}" ]] \
    || fail 'Faltan credenciales o CA de Supabase.'
[[ "$SUPABASE_MIGRATION_URL" != *\?* && "$SUPABASE_MIGRATION_URL" != *\#* ]] \
    || fail 'La URL de migración no debe incluir parámetros ni fragmentos.'

check_main() {
    local current
    current="$(gh api "repos/$GITHUB_REPOSITORY/git/ref/heads/main" --jq .object.sha)"
    [[ "$current" == "$GITHUB_SHA" ]] || fail 'La punta de main ha cambiado. Se omite esta publicación.'
}

render_api() {
    curl --fail --silent --show-error --max-time 15 \
        -H "Authorization: Bearer $RENDER_API_KEY" -H 'Accept: application/json' \
        "$@"
}

check_main
if [[ -n "$service" ]]; then
    render_api "https://api.render.com/v1/services/$service" \
        | jq -e '.branch == "main" and .autoDeployTrigger == "off" and .serviceDetails.plan == "free"' >/dev/null \
        || fail 'Render debe usar el plan Free, main y el autodespliegue desactivado.'
fi

umask 077
ca="$(mktemp)"
trap 'rm -f -- "$ca"' EXIT
printf '%s\n' "$SUPABASE_CA_PEM" > "$ca"
APP_ENV=production APP_DEBUG=false LOG_CHANNEL=null DB_CONNECTION=pgsql \
    DB_URL="$SUPABASE_MIGRATION_URL" DB_SSLMODE=verify-full DB_SSLROOTCERT="$ca" \
    php artisan migrate --force --no-interaction >/dev/null 2>&1 \
    || fail 'Fallaron las migraciones. No se ha solicitado el despliegue.'
printf 'Migraciones aplicadas para %s.\n' "$GITHUB_SHA"

check_main
if [[ -z "$service" ]]; then
    fail 'Publicación pendiente de alta inicial. Registra Render y reejecuta este commit.'
fi

endpoint="https://api.render.com/v1/services/$service/deploys"
deploy_id="$(render_api -H 'Content-Type: application/json' \
    --data "$(jq -nc --arg sha "$GITHUB_SHA" '{commitId:$sha}')" "$endpoint" \
    | jq -er '.id | select(test("^dep-[a-z0-9]+$"))')" \
    || fail 'Render no devolvió un ID de despliegue válido.'

for ((attempt=0; attempt<90; attempt++)); do
    response="$(render_api "$endpoint/$deploy_id")" || fail 'No se pudo consultar el despliegue.'
    status="$(jq -er '.status' <<< "$response")"
    case "$status" in
        live)
            jq -e --arg sha "$GITHUB_SHA" '.commit.id == $sha' <<< "$response" >/dev/null \
                || fail 'Render activó un commit distinto al verificado.'
            curl --fail --silent --show-error --retry 3 --max-time 20 \
                "${RENDER_PUBLIC_URL%/}/up" >/dev/null || fail 'Falló la comprobación HTTPS.'
            printf 'Render activo: %s (%s).\n' "$GITHUB_SHA" "$deploy_id"
            exit 0
            ;;
        created|queued|build_in_progress|pre_deploy_in_progress|update_in_progress) sleep 10 ;;
        *) fail "El despliegue de Render no terminó correctamente ($status)." ;;
    esac
done
fail 'Render no confirmó el despliegue dentro del plazo.'
