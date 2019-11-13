<?php

namespace ByJG\DbMigration\Console;

use League\CLImate\CLImate;
use Exception;

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
