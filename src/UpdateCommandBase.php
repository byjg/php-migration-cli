<?php

namespace ByJG\DbMigration\Console;

use ByJG\DbMigration\Exception\DatabaseDoesNotRegistered;
use ByJG\DbMigration\Exception\DatabaseNotVersionedException;
use ByJG\DbMigration\Exception\OldVersionSchemaException;
use League\CLImate\CLImate;

abstract class UpdateCommandBase extends ConsoleCommand
{
    abstract protected function callMigrate();

    /**
     * @param CLImate $climate
     * @throws DatabaseDoesNotRegistered
     * @throws DatabaseNotVersionedException
     * @throws OldVersionSchemaException
     */
    public function execute(CLimate $climate)
    {
        if (!$this->confirmPartialMigrate($climate)) {
            $climate->out('Aborted.');
            return;
        }
        parent::execute($climate);
        $this->callMigrate();
    }
}
