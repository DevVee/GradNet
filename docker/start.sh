#!/bin/sh
set -e

# =============================================================================
# GradNet — Docker container startup script
# Runs as the container's CMD on every boot.
#
# Key responsibilities:
#   1. Validate and normalize APP_KEY (must be stable — never regenerate at boot)
#   2. Resolve APP_URL from explicit env var or Render's RENDER_EXTERNAL_URL
#   3. Force SESSION_SECURE_COOKIE=true when running on Render (HTTPS)
#   4. Substitute PORT into nginx config template
#   5. Run database migrations (DB is external Supabase — must run at boot, not build)
#   6. Cache Laravel config, routes, and views
#   7. Create storage symlink and fix permissions
#   8. Start supervisord (manages nginx + php-fpm)
# =============================================================================

# ─── 0. Validate and normalize APP_KEY ────────────────────────────────────────
# APP_KEY MUST be a STABLE Render environment variable set manually in the
# Render Dashboard — do NOT use generateValue: true.
# generateValue creates a NEW key on every deploy → all sessions + CSRF tokens
# are invalidated → users see 419 "Session Expired" errors on every redeploy.
#
# Generate a stable key with:  php artisan key:generate --show
# Then set it in Render Dashboard → Environment → APP_KEY
if [ -z "$APP_KEY" ]; then
    echo "FATAL: APP_KEY is not set." >&2
    echo "       Run: php artisan key:generate --show" >&2
    echo "       Then set it as a stable env var in Render Dashboard." >&2
    exit 1
fi

# Normalise: if the key is missing the base64: prefix, add it.
if ! echo "$APP_KEY" | grep -q "^base64:"; then
    APP_KEY="base64:${APP_KEY}"
    export APP_KEY
    echo "==> Normalized APP_KEY: added base64: prefix"
fi

# ─── 1. Resolve APP_URL ────────────────────────────────────────────────────────
APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL:-http://localhost:8080}}"
export APP_URL
ASSET_URL="${APP_URL}"
export ASSET_URL

# ─── 2. Force HTTPS cookies when running on Render ────────────────────────────
if [ -n "$RENDER_EXTERNAL_URL" ]; then
    SESSION_SECURE_COOKIE=true
    export SESSION_SECURE_COOKIE
fi

echo "==> APP_URL        : ${APP_URL}"
echo "==> ASSET_URL      : ${ASSET_URL}"
echo "==> DB_CONNECTION  : ${DB_CONNECTION:-pgsql}"
echo "==> SESSION_DRIVER : ${SESSION_DRIVER:-database}"
echo "==> CACHE_STORE    : ${CACHE_STORE:-database}"
echo "==> LOG_LEVEL      : ${LOG_LEVEL:-error}"
echo "==> Vite manifest  : $(ls /var/www/html/public/build/manifest.json 2>/dev/null && echo 'found' || echo 'MISSING!')"

# ─── 3. Wire nginx to the correct port ───────────────────────────────────────
PORT="${PORT:-8080}"
export PORT
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# ─── 4. Run database migrations ──────────────────────────────────────────────
# Database is external (Supabase PostgreSQL) — no build-time access.
# Migrations run here so every deploy stays schema-consistent.
# Laravel's migration system is idempotent: already-run migrations are skipped.
echo "==> Running database migrations..."
php artisan migrate --force \
    && echo "==> Migrations: done" \
    || echo "==> Migrations: FAILED (check DB_* env vars in Render dashboard)"

# ─── 5. Cache Laravel config / routes / views ────────────────────────────────
# Bakes the current environment variables into serialized PHP caches.
# IMPORTANT: Run AFTER all env vars are finalized (steps 0–3 above).
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ─── 6. Storage symlink and permissions ──────────────────────────────────────
php artisan storage:link --force 2>/dev/null || true

chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache
chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# ─── 7. Start services via supervisor ────────────────────────────────────────
echo ""
echo "==================================================="
echo "  GradNet is starting…"
echo "  URL  : ${APP_URL}"
echo "  Port : ${PORT}"
echo "==================================================="
echo ""

exec supervisord -c /etc/supervisord.conf
