#!/bin/bash

set -e

echo "MIGRATE_CONNECTION=sqlite:///tmp/teste.db
MIGRATE_PATH=example/sqlite/" >> .env

echo "Reset and update the database depending on .env file configurations"
scripts/migrate reset --yes -vvv
scripts/migrate version

echo "Update the database to version 1 depending on .env file configurations"
scripts/migrate update -vvv --up-to=1
scripts/migrate version

echo "Update the database to version 2 depending on .env file configurations"
scripts/migrate up -vvv
scripts/migrate version

echo "Update the database to version 0 (base) depending on .env file configurations"
scripts/migrate down -vvv --up-to=0
scripts/migrate version

echo "Update the database to version 0 (base) depending on .env file configurations"
scripts/migrate up -vvv
scripts/migrate down -vvv
scripts/migrate version

echo "Update the database to version 1 depending on .env file configurations"
scripts/migrate update -vvv --up-to=1
scripts/migrate version

rm .env

export MIGRATE_CONNECTION=sqlite:///tmp/teste.db
export MIGRATE_PATH=example/sqlite/

echo "Reset and update the database"
scripts/migrate reset --yes -vvv
scripts/migrate version

echo "Update the database to version 1"
scripts/migrate update -vvv --up-to=1
scripts/migrate version

echo "Update the database to version 2"
scripts/migrate up -vvv
scripts/migrate version

echo "Update the database to version 0 (base)"
scripts/migrate down -vvv --up-to=0
scripts/migrate version

echo "Update the database to version 0 (base)"
scripts/migrate up -vvv
scripts/migrate down -vvv
scripts/migrate version

echo "Update the database to version 1"
scripts/migrate update -vvv --up-to=1
scripts/migrate version
