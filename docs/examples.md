---
sidebar_position: 4
---

# Examples

Practical examples of common database migration scenarios.

## Starting a New Project

Create the migration structure from scratch:

```bash
# Create directory structure
vendor/bin/migrate create /path/to/migrations

# Edit base.sql with your initial schema
nano /path/to/migrations/base.sql

# Create and initialize database
export MIGRATE_CONNECTION=mysql://user:pass@localhost/myapp
export MIGRATE_PATH=/path/to/migrations

vendor/bin/migrate reset --yes
```

## Adding a New Feature

Scenario: Adding user profile pictures

```bash
# Create new migration files
cd /path/to/migrations/migrations/up
echo "-- Migrate to Version 5
-- Add profile picture support

ALTER TABLE users ADD COLUMN avatar_url VARCHAR(255);
ALTER TABLE users ADD COLUMN avatar_uploaded_at TIMESTAMP NULL;
CREATE INDEX idx_users_avatar ON users(avatar_uploaded_at);" > 00005.sql

cd ../down
echo "-- Rollback from Version 5
-- Remove profile picture support

DROP INDEX idx_users_avatar;
ALTER TABLE users DROP COLUMN avatar_uploaded_at;
ALTER TABLE users DROP COLUMN avatar_url;" > 00005.sql

# Apply migration
vendor/bin/migrate update
```

## Migrating an Existing Database

You have an existing database that needs version control:

```bash
# Step 1: Export current schema as base.sql
mysqldump -u user -p --no-data myapp > /path/to/migrations/base.sql

# Step 2: Create migration structure
mkdir -p /path/to/migrations/migrations/{up,down}

# Step 3: Install version control
export MIGRATE_CONNECTION=mysql://user:pass@localhost/myapp
export MIGRATE_PATH=/path/to/migrations

vendor/bin/migrate install

# Step 4: Verify
vendor/bin/migrate version
```

## Rolling Back a Migration

Scenario: A migration caused issues and needs to be rolled back

```bash
# Check current version
vendor/bin/migrate version
# Output: version: 8

# Rollback to version 7
vendor/bin/migrate down --up-to=7

# Verify rollback
vendor/bin/migrate version
# Output: version: 7
```

## CI/CD Integration

### GitHub Actions

```yaml
name: Database Migration

on:
  push:
    branches: [ main ]

jobs:
  migrate:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: secret
          MYSQL_DATABASE: testdb
        ports:
          - 3306:3306
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'

      - name: Install dependencies
        run: composer install --prefer-dist --no-progress

      - name: Run migrations
        env:
          MIGRATE_CONNECTION: mysql://root:secret@127.0.0.1/testdb
          MIGRATE_PATH: ./database/migrations
        run: |
          vendor/bin/migrate install
          vendor/bin/migrate update -vv
```

### GitLab CI

```yaml
stages:
  - migrate

database_migration:
  stage: migrate
  image: php:8.2

  services:
    - mysql:8.0

  variables:
    MYSQL_ROOT_PASSWORD: secret
    MYSQL_DATABASE: testdb
    MIGRATE_CONNECTION: mysql://root:secret@mysql/testdb
    MIGRATE_PATH: ./database/migrations

  before_script:
    - apt-get update && apt-get install -y git unzip
    - curl -sS https://getcomposer.org/installer | php
    - php composer.phar install

  script:
    - vendor/bin/migrate install
    - vendor/bin/migrate update -vv

  only:
    - main
```

### Docker

```dockerfile
FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y git unzip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Install migration tool
RUN composer require byjg/migration-cli

# Copy migration files
COPY ./migrations /app/migrations

# Run migrations
ENV MIGRATE_PATH=/app/migrations
CMD ["vendor/bin/migrate", "update"]
```

**docker-compose.yml:**

```yaml
version: '3.8'

services:
  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: myapp
    ports:
      - "3306:3306"

  migrate:
    build: .
    environment:
      MIGRATE_CONNECTION: mysql://root:secret@db/myapp
      MIGRATE_PATH: /app/migrations
    depends_on:
      - db
```

Run with:

```bash
docker-compose up -d db
sleep 10  # Wait for MySQL to be ready
docker-compose run migrate vendor/bin/migrate install
docker-compose run migrate vendor/bin/migrate update
```

## Environment-Specific Migrations

### Development

```bash
# .env.development
MIGRATE_CONNECTION=mysql://dev:dev@localhost/myapp_dev
MIGRATE_PATH=./database/migrations
MIGRATE_DISABLE_RESET=false
```

### Staging

```bash
# .env.staging
MIGRATE_CONNECTION=mysql://staging:pass@staging-db/myapp
MIGRATE_PATH=./database/migrations
MIGRATE_DISABLE_RESET=false
```

### Production

```bash
# .env.production
MIGRATE_CONNECTION=mysql://prod:securepass@prod-db/myapp
MIGRATE_PATH=./database/migrations
MIGRATE_DISABLE_RESET=true
MIGRATE_TABLE=migration_version
```

**Script:**

```bash
#!/bin/bash
ENV=$1

if [ -z "$ENV" ]; then
    echo "Usage: $0 [development|staging|production]"
    exit 1
fi

# Load environment
source .env.$ENV

# Run migration
vendor/bin/migrate update -vv

echo "Migration completed for $ENV environment"
```

## Testing Migrations

### PHPUnit Integration

:::warning PHP API Required
The example below uses the **PHP API** from the core library [byjg/migration](https://github.com/byjg/migration), not this CLI package.

To integrate migrations programmatically into your PHP code:
```bash
composer require byjg/migration
```

If you only need the command line tool, use this package (`byjg/migration-cli`) and run migrations via `vendor/bin/migrate` commands in your test setup.
:::

**Example using the PHP API:**

```php
<?php

use ByJG\DbMigration\Database\MySqlDatabase;
use ByJG\DbMigration\Migration;
use ByJG\Util\Uri;
use PHPUnit\Framework\TestCase;

class MigrationTest extends TestCase
{
    private Migration $migration;

    protected function setUp(): void
    {
        $uri = new Uri('mysql://root:secret@localhost/test_db');
        $this->migration = new Migration($uri, __DIR__ . '/migrations');
        Migration::registerDatabase(MySqlDatabase::class);

        // Reset to clean state
        $this->migration->prepareEnvironment();
        $this->migration->reset();
    }

    public function testMigrationToVersion3()
    {
        // Migrate to version 3
        $this->migration->update(3);

        // Verify version
        $version = $this->migration->getCurrentVersion();
        $this->assertEquals(3, $version['version']);
        $this->assertEquals('complete', $version['status']);

        // Verify schema changes
        // ... add assertions for your specific schema
    }

    public function testMigrationRollback()
    {
        // Migrate up
        $this->migration->update(5);

        // Rollback
        $this->migration->down(3);

        // Verify rollback
        $version = $this->migration->getCurrentVersion();
        $this->assertEquals(3, $version['version']);
    }
}
```

**Alternative: Using CLI in Tests**

If you prefer to use this CLI package in your tests:

```php
<?php

use PHPUnit\Framework\TestCase;

class MigrationTest extends TestCase
{
    protected function setUp(): void
    {
        // Run migrations using CLI
        putenv('MIGRATE_CONNECTION=mysql://root:secret@localhost/test_db');
        putenv('MIGRATE_PATH=' . __DIR__ . '/migrations');

        exec('vendor/bin/migrate reset --yes', $output, $returnCode);
        $this->assertEquals(0, $returnCode, 'Migration reset failed');
    }

    public function testDatabaseSchema()
    {
        // Your tests here
        // Database is already migrated by setUp()
    }
}
```

## Multi-Database Support

### SQLite Example

```bash
export MIGRATE_CONNECTION=sqlite:///path/to/database.db
export MIGRATE_PATH=./migrations

vendor/bin/migrate reset --yes
vendor/bin/migrate update
```

### PostgreSQL Example

```bash
export MIGRATE_CONNECTION=pgsql://user:pass@localhost:5432/myapp
export MIGRATE_PATH=./migrations

vendor/bin/migrate install
vendor/bin/migrate update
```

### SQL Server Example

```bash
export MIGRATE_CONNECTION=dblib://user:pass@sqlserver/myapp
export MIGRATE_PATH=./migrations

vendor/bin/migrate install
vendor/bin/migrate update
```

## Migration Scripts with Variables

### Using Environment Variables in SQL

While the migration tool doesn't directly support variables in SQL files, you can use preprocessing:

```bash
#!/bin/bash
# preprocess-migration.sh

VERSION=$1
TABLE_PREFIX=${TABLE_PREFIX:-"app_"}

cat > migrations/up/$(printf "%05d" $VERSION).sql <<EOF
-- Migrate to Version $VERSION
-- Create ${TABLE_PREFIX}settings table

CREATE TABLE ${TABLE_PREFIX}settings (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    key VARCHAR(100) NOT NULL,
    value TEXT,
    UNIQUE KEY(key)
);
EOF

echo "Created migration $VERSION with prefix $TABLE_PREFIX"
```

Usage:

```bash
TABLE_PREFIX="myapp_" ./preprocess-migration.sh 6
vendor/bin/migrate update
```

## Backup Before Migration

Always backup before running migrations in production:

```bash
#!/bin/bash
# safe-migrate.sh

# Configuration
BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="myapp"

# Create backup
mysqldump -u user -p$MYSQL_PASSWORD $DB_NAME > "$BACKUP_DIR/${DB_NAME}_$DATE.sql"

# Run migration
vendor/bin/migrate update -vv

# Check result
if [ $? -eq 0 ]; then
    echo "Migration successful! Backup saved to $BACKUP_DIR/${DB_NAME}_$DATE.sql"
else
    echo "Migration failed! Restore from $BACKUP_DIR/${DB_NAME}_$DATE.sql"
    mysql -u user -p$MYSQL_PASSWORD $DB_NAME < "$BACKUP_DIR/${DB_NAME}_$DATE.sql"
fi
```
