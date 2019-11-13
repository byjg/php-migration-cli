<?php

namespace ByJG\DbMigration\Console;

use League\CLImate\CLImate;
use Exception;

abstract class UpdateCommandBase extends ConsoleCommand
{
    abstract protected function callMigrate();

    public function execute(CLimate $climate)
    {
        try {
            if (!$this->confirmPartialMigrate($climate)) {
                $climate->out('Aborted.');
                return;
            }
            parent::execute($climate);
            $this->callMigrate();
        } catch (Exception $ex) {
            $this->handleError($ex, $climate);
        }
    }
}
