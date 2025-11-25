# Database Migrations (CLI)

[![Build Status](https://github.com/byjg/php-migration-cli/actions/workflows/phpunit.yml/badge.svg?branch=master)](https://github.com/byjg/php-migration-cli/actions/workflows/phpunit.yml)
[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](http://opensource.byjg.com)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/php-migration-cli/)
[![GitHub license](https://img.shields.io/github/license/byjg/php-migration-cli.svg)](https://opensource.byjg.com/opensource/licensing.html)
[![GitHub release](https://img.shields.io/github/release/byjg/php-migration-cli.svg)](https://github.com/byjg/php-migration-cli/releases/)

This is a simple library written in PHP for database version control. Currently supports SQLite, MySQL, SQL Server and PostgreSQL.

Database Migration can be used as:
  - Command Line Interface
  - PHP Library to be integrated in your functional tests
  - Integrated in your CI/CD independent of your programming language or framework
  
## Important Note

This package is the **command line interface** of ByJG PHP Migration.

**Key Points:**
- This CLI tool requires PHP to run, but **your project doesn't need to be written in PHP**
- You can use this tool with any programming language or framework
- Perfect for CI/CD pipelines and database versioning independent of your stack

**For PHP API Integration:**
If you want to integrate migrations directly into your PHP code (e.g., for automated testing or programmatic control), use the core migration library instead:
- **PHP API**: [byjg/migration](https://github.com/byjg/migration) - Full PHP library with programmatic API
- **CLI Tool** (this package): [byjg/migration-cli](https://github.com/byjg/php-migration-cli) - Command line interface only

For complete information about the migration system, architecture, and advanced usage, visit the core project documentation:
[https://github.com/byjg/migration](https://github.com/byjg/migration)


## Installing

```bash
composer require 'byjg/migration-cli'
```

## Documentation

- [Getting Started](docs/getting-started.md) - Installation, configuration, and environment variables
- [Commands Reference](docs/commands.md) - Detailed documentation for all available commands
- [Migration Structure](docs/migration-structure.md) - How to organize and write migration files
- [Examples](docs/examples.md) - Practical examples and CI/CD integration

## Quick Start

The basic usage is:

```bash
vendor/bin/migrate <COMMAND> --path=<scripts> uri://connection
```

**Connection String Examples:**
- SQLite: `sqlite:///path/to/my.db`
- MySQL: `mysql://user:password@server/database`
- PostgreSQL: `pgsql://user:password@server/database`
- SQL Server: `dblib://user:password@server/database`

**Using Environment Variables:**

```bash
export MIGRATE_CONNECTION=sqlite:///path/to/my.db
export MIGRATE_PATH=/path/to/migrate_files
vendor/bin/migrate update
```

Or use a `.env` file:

```env
MIGRATE_CONNECTION=sqlite:///path/to/my.db
MIGRATE_PATH=/path/to/migrate_files
```

## Available Commands

The migration tool provides the following commands:

| Command   | Description                                                            |
|-----------|------------------------------------------------------------------------|
| `create`  | Create the directory structure for migrations                          |
| `install` | Install or upgrade the migration version table in an existing database |
| `version` | Get the current database version                                       |
| `update`  | Intelligently migrate up or down based on current version              |
| `up`      | Migrate up to a newer version                                          |
| `down`    | Migrate down to an older version                                       |
| `reset`   | Create a fresh database from base.sql and apply all migrations         |

### Command Options

All commands (except `create`) support these options:

- `--path, -p`: Define the path where base.sql and migration scripts are located
- `--up-to, -u`: Specify a target version for migration
- `--no-base`: Skip checking for base.sql file
- `-v, -vv, -vvv`: Increase verbosity level

### Quick Examples

```bash
# Create migration structure
vendor/bin/migrate create /path/to/migrations

# Install version control in existing database
vendor/bin/migrate install mysql://user:pass@localhost/myapp

# Check current version
vendor/bin/migrate version mysql://user:pass@localhost/myapp

# Update to latest version
vendor/bin/migrate update mysql://user:pass@localhost/myapp

# Update to specific version
vendor/bin/migrate update --up-to=10 mysql://user:pass@localhost/myapp

# Reset database (WARNING: destructive!)
vendor/bin/migrate reset --yes mysql://user:pass@localhost/myapp
```

## Environment Variables

| Variable                | Description                         | Default             |
|-------------------------|-------------------------------------|---------------------|
| `MIGRATE_CONNECTION`    | Database connection string          | -                   |
| `MIGRATE_PATH`          | Path to migration files             | Current directory   |
| `MIGRATE_TABLE`         | Name of the migration version table | `migration_version` |
| `MIGRATE_DISABLE_RESET` | Disable the reset command           | `false`             |

## Related Projects

- **[Migration Core Library](https://github.com/byjg/migration)** - PHP API for programmatic migration control (use this for PHP code integration)
- [AnyDataset DB](https://github.com/byjg/anydataset-db) - Database abstraction layer
- [Micro ORM](https://github.com/byjg/micro-orm) - Lightweight ORM
- [PHP Rest Template](https://github.com/byjg/php-rest-template) - REST client library

## Dependencies

```mermaid
flowchart TD
    byjg/migration-cli --> byjg/migration
    byjg/migration-cli --> symfony/console
    byjg/migration-cli --> vlucas/phpdotenv
```

----
[Open source ByJG](http://opensource.byjg.com)
