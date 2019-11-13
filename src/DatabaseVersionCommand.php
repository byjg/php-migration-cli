<?php

namespace ByJG\DbMigration\Console;

use ByJG\DbMigration\Exception\DatabaseDoesNotRegistered;
use ByJG\DbMigration\Exception\DatabaseNotVersionedException;
use ByJG\DbMigration\Exception\OldVersionSchemaException;
use League\CLImate\CLImate;

class DatabaseVersionCommand extends ConsoleCommand
{
    public function arguments()
    {
        return parent::arguments();
    }

    public function name() {
        return "version";
    }

    public function description()
    {
        return 'Get the current database version';
    }

    /**
     * @param CLImate $climate
     * @throws DatabaseDoesNotRegistered
     * @throws DatabaseNotVersionedException
     * @throws OldVersionSchemaException
     */
    public function execute(CLimate $climate)
    {
        parent::execute($climate);
        $versionInfo = $this->migration->getCurrentVersion();
        $climate->out('version: ' . $versionInfo['version']);
        $climate->out('status.: ' . $versionInfo['status']);
    }
}
