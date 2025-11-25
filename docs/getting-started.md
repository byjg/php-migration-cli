---
sidebar_position: 1
---

# Getting Started

This is a simple library written in PHP for database version control. Currently supports SQLite, MySQL, SQL Server and PostgreSQL.

:::info CLI vs PHP API
This package (`byjg/migration-cli`) is the **command line interface** for database migrations. While it requires PHP to run, **your project doesn't need to be written in PHP**. You can use this CLI tool with any programming language or framework.

If you need to integrate migrations programmatically into your PHP code (for automated testing, custom workflows, etc.), use the core library instead: [byjg/migration](https://github.com/byjg/migration)
:::

## Installation

Install via Composer:

```bash
composer require 'byjg/migration-cli'
```

## Basic Concepts

Database Migration can be used as:
- **Command Line Interface** (this package) - Run migrations from the terminal with any technology stack
- **PHP Library** ([byjg/migration](https://github.com/byjg/migration)) - Integrate migrations directly in your PHP code
- **CI/CD Integration** - Independent of your programming language or framework

## Quick Start

The basic usage is:

```bash
vendor/bin/migrate <COMMAND> --path=<scripts> uri://connection
```

### Connection String

The connection string is a URI that represents the database connection. Examples:

- SQLite: `sqlite:///path/to/my.db`
- MySQL: `mysql://user:password@server/database`
- PostgreSQL: `pgsql://user:password@server/database`
- SQL Server: `dblib://user:password@server/database`

For more information about connection strings, see the [anydataset-db documentation](https://github.com/byjg/anydataset-db).

## Environment Variables

You can configure the migration tool using environment variables:

### MIGRATE_CONNECTION

Defines the database connection string. This allows you to omit the connection parameter from commands.

```bash
export MIGRATE_CONNECTION=sqlite:///path/to/my.db
```

### MIGRATE_PATH

Specifies where the base.sql and migration scripts are located. Defaults to the current directory.

```bash
export MIGRATE_PATH=/path/to/migrate_files
```

### MIGRATE_TABLE

Defines the name of the migration version table. Defaults to `migration_version`.

```bash
export MIGRATE_TABLE=my_migration_version
```

### MIGRATE_DISABLE_RESET

Disables the `reset` command to prevent accidental data loss in production environments.

```bash
export MIGRATE_DISABLE_RESET=true
```

### Using .env Files

You can also define these variables in a `.env` file in your project root:

```env
MIGRATE_CONNECTION=sqlite:///path/to/my.db
MIGRATE_PATH=/path/to/migrate_files
MIGRATE_TABLE=migration_version
MIGRATE_DISABLE_RESET=false
```

The package will automatically discover and load these variables.
