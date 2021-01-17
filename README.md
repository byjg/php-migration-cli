# Database Migrations (Cli)

[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](http://opensource.byjg.com)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/migration-cli/)
[![GitHub license](https://img.shields.io/github/license/byjg/migration-cli.svg)](https://opensource.byjg.com/opensource/licensing.html)
[![GitHub release](https://img.shields.io/github/release/byjg/migration-cli.svg)](https://github.com/byjg/migration-cli/releases/)
[![Build Status](https://travis-ci.com/byjg/migration-cli.svg?branch=master)](https://travis-ci.com/byjg/migration-cli)

This is a simple library written in PHP for database version control. Currently supports Sqlite, MySql, Sql Server and Postgres.

Database Migration can be used as:
  - Command Line Interface
  - PHP Library to be integrated in your functional tests
  - Integrated in you CI/CD indenpent of your programming language or framework.
  
## Important Note

This package is the command line interface of ByJG PHP Migration. 
To get more information about the the project and how to please visit:
[https://github.com/byjg/migration](https://github.com/byjg/migration)


## Installing

```
composer require 'byjg/migration-cli=4.1.*'
```

## Running in the command line

Migration library creates the 'migrate' script. It has the follow syntax:

```
Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  create   Create the directory structure FROM a pre-existing database
  down     Migrate down the database version.
  help     Displays help for a command
  install  Install or upgrade the migrate version in a existing database
  list     Lists commands
  reset    Create a fresh new database
  up       Migrate Up the database version
  update   Migrate Up or Down the database version based on the current database version and the migration scripts available
  version  Get the current database version
```

## Commands

### Basic Usage

The basic usage is:

```text
vendor/bin/migrate <COMMAND> --path=<scripts> uri://connection
```

The `--path` specify where the base.sql and migrate scripts are located. 
If you omitted the `--path` it will assume the current directory. You can also
set the `MIGRATE_PATH` environment variable with the base path 

The uri://connection is the uri that represents the connection to the database. 
You can see [here](https://github.com/byjg/anydataset-db)
to know more about the connection string.

You can omit the uri parameter if you define it in the 
`MIGRATE_CONNECTION` environment variable and the parameter path with
`MIGRATE_PATH` environment variable

```bash
export MIGRATE_CONNECTION=sqlite:///path/to/my.db
export MIGRATE_PATH=/path/to/migrate_files
```
  
### Command: create

Create a empty directory structure with base.sql and migrations/up and migrations/down for migrations. This is
useful for create from scratch a migration scheme.

Ex.

```bash
migrate create /path/to/sql 
```

### Command: install 

If you already have a database but it is not controlled by the migration system you can use this method for 
install the required tables for migration.

```bash
migrate install mysql://server/database
```

### Command: update

Will apply all necessary migrations to keep your database updated.

```bash
migrate update mysql://server/database
```

Update command can choose if up or down your database depending on your current database version.
You can also specify a version: 

```bash
migrate update --up-to=34
``` 

### Command: reset

Creates/replace a database with the "base.sql" and apply ALL migrations

```bash
migrate reset            # reset the database and apply all migrations scripts.
migrate reset --up-to=5  # reset the database and apply the migration from the 
                         # start up to the version 5.
migrate reset --yes      # reset the database without ask anything. Be careful!!
```

**Note on reset:** You can disable the reset command by setting the environment variable 
`MIGRATE_DISABLE_RESET` to true:

```bash
export MIGRATE_DISABLE_RESET=true
```

## Related Projects

- [Micro ORM](https://github.com/byjg/micro-orm)
- [Anydataset](https://github.com/byjg/anydataset)
- [PHP Rest Template](https://github.com/byjg/php-rest-template)
- [USDocker](https://github.com/usdocker/usdocker)

----
[Open source ByJG](http://opensource.byjg.com)
