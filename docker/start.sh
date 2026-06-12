#!/bin/sh
set -e

# =============================================================================
# GradNet — Docker container startup script
# Runs as the container's CMD (via supervisord) on every boot.
#
# Key responsibilities:
#   1. Validate and normalize APP_KEY (must be stable — never regenerate at boot)
#   2. Resolve APP_URL from explicit env var or Render's RENDER_EXTERNAL_URL
#   3. Force SESSION_SECURE_COOKIE=true when running on Render (HTTPS)
#   4. Substitute PORT into nginx config template
#   5. Set SQLite WAL mode for concurrent php-fpm workers
#   6. Cache Laravel config, routes, and views (bakes env vars into PHP cache)
#   7. Fix filesystem permissions
#   8. Start supervisord (manages nginx + php-fpm)
# =============================================================================

# ─── 0. Validate and normalize APP_KEY ────────────────────────────────────────
# APP_KEY MUST be set as a STABLE Render environment variable — a static value,
# NOT generateValue: true. Render's generateValue produces a new raw string on
# every deploy, and this script would wrap it in base64: each boot, producing
# a DIFFERENT key each time → invalidates all sessions + CSRF tokens → 419 errors.
if [ -z "$APP_KEY" ]; then
    echo "FATAL: APP_KEY is not set." >&2
    echo "       Add a static 'base64:...' key as a Render env var." >&2
    echo "       Generate one with: php artisan key:generate --show" >&2
    exit 1
fi

# If APP_KEY is a raw string (no base64: prefix), add the prefix.
if ! echo "$APP_KEY" | grep -q "^base64:"; then
    APP_KEY="base64:${APP_KEY}"
    export APP_KEY
    echo "==> Normalized APP_KEY: added base64: prefix (key content unchanged)"
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

echo "==> APP_URL       : ${APP_URL}"
echo "==> ASSET_URL     : ${ASSET_URL}"
echo "==> SESSION_DRIVER: ${SESSION_DRIVER:-database}"
echo "==> CACHE_STORE   : ${CACHE_STORE:-database}"
echo "==> LOG_LEVEL     : ${LOG_LEVEL:-error}"
echo "==> Vite manifest : $(ls /var/www/html/public/build/manifest.json 2>/dev/null && echo 'found' || echo 'MISSING!')"

# ─── 3. Wire nginx to the correct port ───────────────────────────────────────
PORT="${PORT:-8080}"
export PORT
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# ─── 4. Set SQLite WAL mode (permanent, stored in the .sqlite file header) ─────
# WAL allows concurrent readers while a writer is active — essential under
# nginx + php-fpm multi-worker. Without it, write contention produces
# "database is locked" 500 errors under simultaneous requests.
# The PRAGMA is idempotent: safe to run every boot even if already set.
DB_FILE="/var/www/html/database/database.sqlite"
if [ -f "$DB_FILE" ]; then
    sqlite3 "$DB_FILE" \
        "PRAGMA journal_mode=WAL; PRAGMA synchronous=NORMAL; PRAGMA busy_timeout=5000;" \
        2>/dev/null && echo "==> SQLite WAL mode: enabled" \
        || echo "==> SQLite WAL mode: sqlite3 not found — config-level fallback active"
fi

# ─── 5. Cache Laravel config / routes / views ────────────────────────────────
# This bakes the current environment variables (APP_URL, APP_KEY, SESSION_DRIVER,
# etc.) into serialized PHP caches in bootstrap/cache/.
# IMPORTANT: Run AFTER all env vars are finalized (steps 0–3 above).
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ─── 6. Storage symlink ───────────────────────────────────────────────────────
php artisan storage:link --force 2>/dev/null || true

# ─── 7. Fix permissions (covers volume-mount edge cases) ──────────────────────
chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    /var/www/html/database
chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache
chmod 664 /var/www/html/database/database.sqlite

# ─── 8. Start services via supervisor ────────────────────────────────────────
echo ""
echo "==================================================="
echo "  GradNet is starting…"
echo "  URL  : ${APP_URL}"
echo "  Port : ${PORT}"
echo "==================================================="
echo ""

exec supervisord -c /etc/supervisord.conf
