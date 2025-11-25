---
sidebar_position: 3
---

# Migration Structure

Understanding how to organize your migration files is essential for effective database version control.

## Directory Structure

A typical migration setup looks like this:

```
project/
├── base.sql
└── migrations/
    ├── up/
    │   ├── 00001.sql
    │   ├── 00002.sql
    │   └── 00003.sql
    └── down/
        ├── 00001.sql
        ├── 00002.sql
        └── 00003.sql
```

## base.sql

The `base.sql` file contains the initial database schema. This is the foundation that will be created when running the `reset` command.

**Example:**

```sql
-- base.sql
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

:::tip
Keep `base.sql` minimal and let migrations handle schema evolution. This makes it easier to track changes over time.
:::

## Migration Scripts

Migration scripts are numbered SQL files that describe incremental changes to your database schema.

### Naming Convention

Migration files must follow this naming pattern:

```
00001.sql
00002.sql
00003.sql
...
99999.sql
```

- Files are numbered with 5 digits, zero-padded
- Numbers must be sequential
- The system executes migrations in numerical order

### Up Migrations

Located in `migrations/up/`, these scripts upgrade the database schema.

**Example: migrations/up/00001.sql**

```sql
-- Migrate to Version 1
-- Add status column to users table

ALTER TABLE users ADD COLUMN status VARCHAR(20) DEFAULT 'active';
```

**Example: migrations/up/00002.sql**

```sql
-- Migrate to Version 2
-- Create comments table

CREATE TABLE comments (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    post_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Down Migrations

Located in `migrations/down/`, these scripts reverse the changes made by up migrations.

**Example: migrations/down/00001.sql**

```sql
-- Rollback from Version 1
-- Remove status column from users table

ALTER TABLE users DROP COLUMN status;
```

**Example: migrations/down/00002.sql**

```sql
-- Rollback from Version 2
-- Drop comments table

DROP TABLE comments;
```

:::warning
Always test your down migrations! They should cleanly reverse the corresponding up migration.
:::

## Creating Migrations

### Manual Creation

1. Determine the next version number
2. Create corresponding files in both `up/` and `down/` directories
3. Write SQL for the schema change (up) and its reversal (down)

### Using the create Command

```bash
vendor/bin/migrate create --path=/path/to/sql --migration
```

This automatically generates the next numbered migration files with a basic template.

## Best Practices

### Keep Migrations Atomic

Each migration should represent a single, logical change:

✅ Good:
```
00001.sql - Add user status column
00002.sql - Create comments table
00003.sql - Add index to posts.created_at
```

❌ Bad:
```
00001.sql - Add status, create comments, modify posts, etc.
```

### Make Migrations Reversible

Always write corresponding down migrations that properly reverse changes:

```sql
-- Up: migrations/up/00003.sql
CREATE INDEX idx_posts_created_at ON posts(created_at);

-- Down: migrations/down/00003.sql
DROP INDEX idx_posts_created_at;
```

### Comment Your Migrations

Add clear comments explaining what each migration does:

```sql
-- Migrate to Version 4
-- Add full-text search index to posts table
-- Required for implementing search feature in v2.0

ALTER TABLE posts ADD FULLTEXT KEY ft_title_content (title, content);
```

### Test Before Deployment

1. Test up migrations on a copy of production data
2. Verify down migrations can properly rollback
3. Check for data loss or corruption
4. Validate foreign key constraints

### Handle Data Migrations Carefully

When migrations involve data transformations:

```sql
-- Migrate to Version 5
-- Split name column into first_name and last_name

-- Add new columns
ALTER TABLE users
    ADD COLUMN first_name VARCHAR(50),
    ADD COLUMN last_name VARCHAR(50);

-- Migrate existing data
UPDATE users
SET
    first_name = SUBSTRING_INDEX(name, ' ', 1),
    last_name = SUBSTRING_INDEX(name, ' ', -1);

-- Remove old column
ALTER TABLE users DROP COLUMN name;
```

## Migration Version Table

The migration tool tracks versions in a special table (default: `migration_version`).

**Schema:**

| Column | Type | Description |
|--------|------|-------------|
| version | INTEGER | Current migration version |
| status | VARCHAR | Migration status (complete, partial, etc.) |
| timestamp | TIMESTAMP | When the migration was applied |

You can query this table to check the current database state:

```sql
SELECT * FROM migration_version;
```

## Troubleshooting

### Migration Failed Partially

If a migration fails partway through:

1. Check the `migration_version` table status
2. Manually fix the database state
3. Update the version table or run the migration again

### Version Mismatch

If you have version conflicts:

```bash
# Check current version
vendor/bin/migrate version mysql://server/database

# Install or upgrade version table
vendor/bin/migrate install mysql://server/database

# Update to correct version
vendor/bin/migrate update --up-to=X mysql://server/database
```

### Skip base.sql Check

If you don't want to use `base.sql`:

```bash
vendor/bin/migrate update --no-base mysql://server/database
```
