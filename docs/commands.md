---
sidebar_position: 2
---

# Commands Reference

The migration tool provides several commands to manage your database versions.

## create

Create an empty directory structure with `base.sql` and migration folders. Useful for starting a new migration scheme from scratch.

**Usage:**

```bash
vendor/bin/migrate create /path/to/sql
vendor/bin/migrate create /path/to/sql --migration
```

**Options:**

- `--migration, -m`: Also create migration script templates (up and down)

**What it creates:**

```
/path/to/sql/
├── base.sql
└── migrations/
    ├── up/
    └── down/
```

## install

Install or upgrade the migration version control in an existing database. Use this when you have a database that isn't yet controlled by the migration system.

**Usage:**

```bash
vendor/bin/migrate install mysql://server/database
vendor/bin/migrate install --path=/custom/path mysql://server/database
```

**What it does:**

- Creates the migration version table if it doesn't exist
- Upgrades the version table schema if needed
- Reports the current version and status

## version

Get the current database version and migration status.

**Usage:**

```bash
vendor/bin/migrate version mysql://server/database
```

**Output:**

```
version: 42
status.: complete
```

## update

Intelligently migrate the database up or down based on the current version and available migration scripts.

**Usage:**

```bash
vendor/bin/migrate update mysql://server/database
vendor/bin/migrate update --up-to=34 mysql://server/database
```

**Options:**

- `--up-to=VERSION, -u`: Migrate to a specific version (up or down)
- `--path=PATH, -p`: Define the path where base.sql resides
- `--no-base`: Skip checking for base.sql file

**Behavior:**

- Automatically determines whether to migrate up or down
- Applies all necessary migrations to reach the target version
- If no `--up-to` is specified, migrates to the latest version

## up

Migrate the database up to a newer version.

**Usage:**

```bash
vendor/bin/migrate up mysql://server/database
vendor/bin/migrate up --up-to=10 --path=/some/path mysql://server/database
```

**Options:**

- `--up-to=VERSION, -u`: Migrate up to a specific version
- `--path=PATH, -p`: Define the path where base.sql resides
- `--no-base`: Skip checking for base.sql file

## down

Migrate the database down to an older version.

**Usage:**

```bash
vendor/bin/migrate down mysql://server/database
vendor/bin/migrate down --up-to=3 --path=/some/path mysql://server/database
```

**Options:**

- `--up-to=VERSION, -u`: Migrate down to a specific version
- `--path=PATH, -p`: Define the path where base.sql resides
- `--no-base`: Skip checking for base.sql file

**Warning:** Downgrading may result in data loss depending on your migration scripts.

## reset

Creates or replaces a database with the `base.sql` and applies all migrations. **This will ERASE all data!**

**Usage:**

```bash
vendor/bin/migrate reset mysql://server/database
vendor/bin/migrate reset --up-to=5 mysql://server/database
vendor/bin/migrate reset --yes mysql://server/database
```

**Options:**

- `--up-to=VERSION, -u`: Reset and migrate up to a specific version
- `--yes`: Skip confirmation prompt (use with caution!)
- `--path=PATH, -p`: Define the path where base.sql resides

**Safety:**

- Prompts for confirmation unless `--yes` is used
- Can be disabled by setting `MIGRATE_DISABLE_RESET=true`
- **Never use in production without proper backups!**

## Common Options

All commands (except `create`) support these options:

### --path, -p

Define the path where `base.sql` and migration scripts are located.

```bash
vendor/bin/migrate update --path=/custom/path mysql://server/database
```

### --up-to, -u

Specify a target version for migration.

```bash
vendor/bin/migrate update --up-to=42 mysql://server/database
```

### --no-base

Remove the check for `base.sql` file. Useful when you don't need a base schema.

```bash
vendor/bin/migrate update --no-base mysql://server/database
```

### Verbosity

Control output verbosity with standard Symfony Console options:

- `-v`: Verbose output (shows connection details)
- `-vv`: Very verbose output (shows migration progress)
- `-vvv`: Debug output

```bash
vendor/bin/migrate update -vv mysql://server/database
```
