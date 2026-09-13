#!/usr/bin/env sh

if [ "$APP_ENV" != "local" ]; then
  echo "Skipping schema dump (APP_ENV != local)"
  exit 0
fi

echo "Dumping database tables..."

DB_NAME="${DB_NAME:-app}"
DB_USER="${DB_USER:-root}"
DB_PASSWORD="${DB_PASSWORD:-root}"
DB_HOST="${DB_HOST:-db}"

OUTPUT_DIR="/app/var/schema"
mkdir -p "$OUTPUT_DIR"

TABLES=$(mariadb \
  --ssl-mode=DISABLED \
  -h"$DB_HOST" \
  -u"$DB_USER" \
  -p"$DB_PASSWORD" \
  -N -e "SHOW TABLES FROM $DB_NAME" || true)

if [ -z "$TABLES" ]; then
  echo "No tables found, skipping dump"
  exit 0
fi

for TABLE in $TABLES; do
  echo "Dumping $TABLE..."

  mariadb-dump \
    --ssl-mode=DISABLED \
    -h"$DB_HOST" \
    -u"$DB_USER" \
    -p"$DB_PASSWORD" \
    --no-data \
    --skip-comments \
    --compact \
    "$DB_NAME" "$TABLE" > "$OUTPUT_DIR/$TABLE.sql"

  sed -i 's/ENGINE=[^ ]*/ENGINE=MEMORY/g' "$OUTPUT_DIR/$TABLE.sql"
  sed -i 's/AUTO_INCREMENT=[0-9]*//g' "$OUTPUT_DIR/$TABLE.sql"
done

echo "Done dumping tables."
