#!/bin/sh
set -e

#echo "[INIT] Waiting for DB..."
#
#until mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
#  sleep 1
#done

#echo "[INIT] DB ready"

echo "[INIT] Running migrations..."
/migrate.sh

echo "[INIT] Dumping tables..."
/dump_tables.sh

echo "[INIT] Starting app..."

exec "$@"