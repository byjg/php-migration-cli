<?php

namespace ByJG\DbMigration\Console;

use League\CLImate\CLImate;
use Exception;

abstract class UpdateCommandBase extends ConsoleCommand
{
    abstract protected function callMigrate();

    public function arguments()
    {
        $arguments = parent::arguments();
        $arguments["up-to"]["required"] = true;
        return $arguments;
    }

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
