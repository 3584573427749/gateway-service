#!/bin/sh
set -e

echo "Running database migrations..."

php vendor/bin/phinx migrate || echo "Phinx migrate failed or no database/migrations available. Continuing startup."

echo "Migrations done..."

