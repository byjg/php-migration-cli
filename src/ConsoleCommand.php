<?php

namespace ByJG\DbMigration\Console;

use ByJG\DbMigration\Database\DblibDatabase;
use ByJG\DbMigration\Database\MySqlDatabase;
use ByJG\DbMigration\Database\PgsqlDatabase;
use ByJG\DbMigration\Database\SqliteDatabase;
use ByJG\DbMigration\Exception\DatabaseDoesNotRegistered;
use ByJG\DbMigration\Exception\DatabaseNotVersionedException;
use ByJG\DbMigration\Exception\InvalidMigrationFile;
use ByJG\DbMigration\Exception\OldVersionSchemaException;
use ByJG\DbMigration\Migration;
use ByJG\Util\Uri;
use InvalidArgumentException;
use League\CLImate\CLImate;

abstract class ConsoleCommand extends Command
{
    public function arguments()
    {
        $arguments = [
            "connection" => [
                "longPrefix" => "connection",
                "description" => 'The connection string. Ex. mysql://root:password@server/database',
                "required" => true,
                "defaultValue" => getenv('MIGRATE_CONNECTION')
            ],
            "path" => [
                "prefix" => "p",
                "longPrefix" => "path",
                "description" => 'Define the path where the base.sql is located.',
                "required" => true,
                "defaultValue" => getenv('MIGRATE_PATH')
            ],
            "up-to" => [
                "prefix" => "u",
                "longPrefix" => "up-to",
                "description" => 'Run up to the specified version',
                "required" => false,
            ],
            'verbose' => [
                'prefix'      => 'v',
                'longPrefix'  => 'verbose',
                'description' => 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug',
                'castTo' => "int",
            ],
            'help' => [
                'prefix'      => 'h',
                'longPrefix'  => 'help',
                'noValue' => true,
                'required' => false,
                'description' => 'This help',
            ],
            'yes' => [
                'longPrefix'  => 'yes',
                'noValue' => true,
                'required' => false,
                'description' => 'Answer yes to any interactive question',
            ]
        ];

        if (empty($arguments["connection"]["defaultValue"])) {
            unset($arguments["connection"]["defaultValue"]);
        }
        if (empty($arguments["path"]["defaultValue"])) {
            unset($arguments["path"]["defaultValue"]);
        }

        return $arguments;
    }

    /**
     * @var Migration
     */
    protected $migration;

    protected $upTo;

    protected $connection;

    protected $path;

    protected $verbose;

    protected $yes;

    /**
     * @param CLImate $climate
     * @throws InvalidMigrationFile
     */
    public function initialize(CLImate $climate)
    {
        $this->connection = $climate->arguments->get('connection');
        if (!$this->connection) {
            throw new InvalidArgumentException(
                'You need to setup the connection in the argument or setting the environment MIGRATE_CONNECTION'
            );
        }

        $this->path = $climate->arguments->get('path');
        if (!$this->path) {
            $this->path = (!empty(getenv('MIGRATE_PATH')) ? getenv('MIGRATE_PATH') : ".");
        }
        $this->path = realpath($this->path);

        $this->upTo = $climate->arguments->get('up-to');
        if ($this->upTo === "") {
            $this->upTo = null;
        }

        $this->yes = $climate->arguments->get("yes");

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
     * @param $climate
     * @return bool
     * @throws DatabaseDoesNotRegistered
     * @throws DatabaseNotVersionedException
     * @throws OldVersionSchemaException
     */
    protected function confirmPartialMigrate(CLImate $climate)
    {
        $versionInfo = $this->migration->getCurrentVersion();
        if (strpos($versionInfo['status'], 'partial') !== false) {
            $message = 'The database was not fully updated and maybe be unstable.';
            if ($this->yes) {
                $climate->out("$message Assumed yes.");
                return true;
            }

            $input = $climate->radio("$message Did you really want migrate the version?", ['No', 'Yes']);
            $response = $input->prompt();
            return ($response == 'Yes');
        }

        return true;
    }
}
