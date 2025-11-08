<?php

namespace ByJG\DbMigration\Console;

use ByJG\DbMigration\Exception\DatabaseDoesNotRegistered;
use ByJG\DbMigration\Exception\DatabaseIsIncompleteException;
use ByJG\DbMigration\Exception\DatabaseNotVersionedException;
use ByJG\DbMigration\Exception\InvalidMigrationFile;
use ByJG\DbMigration\Exception\OldVersionSchemaException;

class UpCommand extends UpdateCommandBase
{
    #[\Override]
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('up')
            ->setDescription('Migrate Up the database version');

    }

    /**
     * @throws DatabaseDoesNotRegistered
     * @throws DatabaseIsIncompleteException
     * @throws DatabaseNotVersionedException
     * @throws InvalidMigrationFile
     * @throws OldVersionSchemaException
     *
     * @return void
     */
    #[\Override]
    protected function callMigrate(): void
    {
        $this->migration->up($this->upTo, true);
    }
}
