<?php

namespace ByJG\DbMigration\Console;

use ByJG\DbMigration\Database\DblibDatabase;
use ByJG\DbMigration\Database\MySqlDatabase;
use ByJG\DbMigration\Database\PgsqlDatabase;
use ByJG\DbMigration\Database\SqliteDatabase;
use ByJG\DbMigration\Exception\InvalidMigrationFile;
use ByJG\DbMigration\Migration;
use ByJG\Util\Uri;
use League\CLImate\CLImate;
use Exception;
use Error;

abstract class ConsoleCommand extends Command
{
    public function arguments()
    {
        return [
            "connection" => [
                "longPrefix" => "connection",
                "description" => 'The connection string. Ex. mysql://root:password@server/database',
                "required" => false,
                "defaultValue" => getenv('MIGRATE_CONNECTION')
            ],
            "path" => [
                "prefix" => "p",
                "longPrefix" => "path",
                "description" => 'Define the path where the base.sql resides. If not set assumes the current folder',
                "required" => true,
            ],
            "up-to" => [
                "prefix" => "u",
                "longPrefix" => "up-to",
                "description" => 'Run up to the specified version',
                "required" => true,
            ],
            'verbose' => [
                'prefix'      => 'v',
                'longPrefix'  => 'verbose',
                'description' => 'Verbose output',
                'castTo' => "int",
            ],
            'help' => [
                'prefix'      => 'h',
                'longPrefix'  => 'help',
                'noValue' => true,
                'required' => false,
                'description' => 'This help',
            ],
        ];
    }

    /**
     * @var Migration
     */
    protected $migration;

    protected $upTo;

    protected $connection;

    protected $path;

    protected $verbose;

    /**
     * @param CLImate $climate
     * @throws InvalidMigrationFile
     */
    public function initialize(CLImate $climate)
    {
        $this->connection = $climate->arguments->get('connection');
        if (!$this->connection) {
            throw new \InvalidArgumentException(
                'You need to setup the connection in the argument or setting the environment MIGRATE_CONNECTION'
            );
        }

        $this->path = $climate->arguments->get('path');
        if (!$this->path) {
            $this->path = (!empty(getenv('MIGRATE_PATH')) ? getenv('MIGRATE_PATH') : ".");
        }
        $this->path = realpath($this->path);

        $this->upTo = $climate->arguments->get('up-to');

        $requiredBase = !$climate->arguments->get('no-base');

        $this->verbose = $climate->arguments->get('verbose');

        $migrationTable = (empty(getenv('MIGRATE_TABLE')) ? "migration_version" : getenv('MIGRATE_TABLE'));
        $this->path = realpath($this->path);
        $uri = new Uri($this->connection);
        $this->migration = new Migration($uri, $this->path, $requiredBase, $migrationTable);
        $this->migration
            ->registerDatabase('sqlite', SqliteDatabase::class)
            ->registerDatabase('mysql', MySqlDatabase::class)
            ->registerDatabase('pgsql', PgsqlDatabase::class)
            ->registerDatabase('dblib', DblibDatabase::class);
    }

    /**
     * @param CLImate $climate
     */
    public function execute(CLImate $climate)
    {
        if ($this->verbose >= 1) {
            $climate->out('Connection String: ' . $this->connection);
            $climate->out('Path: ' . $this->path);
        }

        if ($this->verbose >= 2) {
            $this->migration->addCallbackProgress(function ($command, $version) use ($climate) {
                $climate->out('Doing: ' . $command . " to " . $version);
            });
        }
    }

    /**
     * @param Exception|Error $exception
     * @param CLImate $climate
     */
    protected function handleError($exception, CLImate $climate)
    {
        $climate->out('-- Error migrating tables --');
        if ($this->verbose >= 1) {
            $climate->out(get_class($exception));
            $climate->out($exception->getMessage());
        }
    }
}
