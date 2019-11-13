<?php

namespace ByJG\DbMigration\Console;

use ByJG\DbMigration\Exception\DatabaseDoesNotRegistered;
use ByJG\DbMigration\Exception\DatabaseIsIncompleteException;
use ByJG\DbMigration\Exception\DatabaseNotVersionedException;
use ByJG\DbMigration\Exception\InvalidMigrationFile;
use ByJG\DbMigration\Exception\OldVersionSchemaException;
use League\CLImate\CLImate;

class DownCommand extends ConsoleCommand
{
    public function arguments()
    {
        $arguments = parent::arguments();
        $arguments["up-to"]["required"] = true;
        return $arguments;
    }

    public function name()
    {
        return 'down';
    }

    public function description()
    {
        return 'Migrate down the database version.';
    }

    /**
     * @param CLImate $climate
     * @throws DatabaseDoesNotRegistered
     * @throws DatabaseIsIncompleteException
     * @throws DatabaseNotVersionedException
     * @throws InvalidMigrationFile
     * @throws OldVersionSchemaException
     */
    public function execute(CLimate $climate)
    {
        if (!$this->confirmPartialMigrate($climate)) {
            $climate->out('Aborted.');
            return;
        }
        parent::execute($climate);
        $this->migration->down($this->upTo, true);
    }
}
