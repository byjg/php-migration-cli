<?php

namespace ByJG\DbMigration\Console;

use ByJG\DbMigration\Exception\DatabaseDoesNotRegistered;
use ByJG\DbMigration\Exception\DatabaseIsIncompleteException;
use ByJG\DbMigration\Exception\DatabaseNotVersionedException;
use ByJG\DbMigration\Exception\InvalidMigrationFile;
use ByJG\DbMigration\Exception\OldVersionSchemaException;

class UpCommand extends UpdateCommandBase
{
    public function arguments()
    {
        $arguments = parent::arguments();
        $arguments["up-to"]["required"] = true;
        return $arguments;
    }

    public function name()
    {
        return 'up';
    }

    public function description()
    {
        return 'Migrate Up the database version';
    }

    /**
     * @throws DatabaseDoesNotRegistered
     * @throws DatabaseIsIncompleteException
     * @throws DatabaseNotVersionedException
     * @throws InvalidMigrationFile
     * @throws OldVersionSchemaException
     */
    protected function callMigrate()
    {
        $this->migration->up($this->upTo, true);
    }
}
