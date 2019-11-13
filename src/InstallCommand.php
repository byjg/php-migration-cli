<?php

namespace ByJG\DbMigration\Console;

use ByJG\DbMigration\Exception\DatabaseNotVersionedException;
use ByJG\DbMigration\Exception\OldVersionSchemaException;
use League\CLImate\CLImate;
use Exception;

class InstallCommand extends ConsoleCommand
{
    public function name()
    {
        return 'install';

    }

    public function description()
    {
        return 'Install or upgrade the migrate version in a existing database';
    }

    /**
     * @param CLImate $climate
     * @return int|null|void
     * @throws DatabaseNotVersionedException
     * @throws OldVersionSchemaException
     * @throws \ByJG\DbMigration\Exception\DatabaseDoesNotRegistered
     */
    public function execute(CLimate $climate)
    {
        parent::execute($climate);

        $action = 'Database is already versioned. ';
        try {
            $this->migration->getCurrentVersion();
        } catch (DatabaseNotVersionedException $ex) {
            $action = 'Created the version table';
            $this->migration->createVersion();
        } catch (OldVersionSchemaException $ex) {
            $action = 'Updated the version table';
            $this->migration->updateTableVersion();
        }

        $version = $this->migration->getCurrentVersion();
        $climate->out($action);
        $climate->out('current version: ' . $version['version']);
        $climate->out('current status.: ' . $version['status']);
    }
}
