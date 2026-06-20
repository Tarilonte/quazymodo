#!/bin/sh
set -eu

# Shared runtime directories need broad write access in local bind mounts,
# because host ownership overrides image-time chown during compose runs.
APP_ROOT="/var/www/html"
WRITABLE_DIR="$APP_ROOT/app/writable"
TRACY_DIR="$WRITABLE_DIR/tracy"
TRACY_CLI_DIR="$TRACY_DIR/cli"

mkdir -p "$TRACY_DIR"
mkdir -p "$TRACY_CLI_DIR"

# Keep container runtime ownership aligned with php-fpm when possible.
chown -R nginx:nginx "$TRACY_DIR" 2>/dev/null || true

# Allow nginx, host user and CLI to append Tracy logs in shared dev setups.
chmod -R ugo+rwX "$TRACY_DIR" 2>/dev/null || true

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
