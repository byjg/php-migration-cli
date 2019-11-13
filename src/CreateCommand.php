<?php

namespace ByJG\DbMigration\Console;

use League\CLImate\CLImate;

class CreateCommand extends Command
{
    public function arguments()
    {
        return [
            "path" => [
                "longPrefix" => "path",
                "description" => 'Define the path where the base.sql resides.',
                "required" => true
            ],
            "migration" => [
                "prefix" => "m",
                "longPrefix" => "migration",
                "description" => 'Create the migration script (Up and Down)',
                "noValue" => true
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

    public function name()
    {
        return 'create';
    }

    public function description()
    {
        return 'Create the directory structure FROM a pre-existing database';
    }

    public function initialize(CLimate $climate)
    {
    }

    protected function createMigrationSql($path, $startVersion)
    {
        $files = glob("$path/*.sql");
        $lastVersion = $startVersion;
        foreach ($files as $file) {
            $version = intval(basename($file));
            if ($version > $lastVersion) {
                $lastVersion = $version;
            }
        }

        $lastVersion = $lastVersion + 1;

        file_put_contents(
            "$path/" . str_pad($lastVersion, 5, '0', STR_PAD_LEFT) . ".sql",
            "-- Migrate to Version $lastVersion \n\n"
        );

        return $lastVersion;
    }

    public function execute(CLimate $climate)
    {
        $path = $climate->arguments->get('path');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if (!file_exists("$path/base.sql")) {
            file_put_contents("$path/base.sql", "-- Put here your base SQL");
        }

        if (!file_exists("$path/migrations")) {
            mkdir("$path/migrations", 0777, true);
            mkdir("$path/migrations/up", 0777, true);
            mkdir("$path/migrations/down", 0777, true);
        }

        if ($climate->arguments->get('migration')) {
            $climate->out('Created UP version: ' . $this->createMigrationSql("$path/migrations/up", 0));
            $climate->out('Created DOWN version: ' . $this->createMigrationSql("$path/migrations/down", -1));
        }
    }
}
