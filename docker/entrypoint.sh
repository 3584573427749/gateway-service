#!/bin/sh
set -e

echo "Starting gw-service..."

if [ ! -f .env ]; then
    echo "No .env file found."
    echo "Copying .env.example -> .env"
    cp .env.example .env
else
    echo ".env already exists."
fi

exec "$@"