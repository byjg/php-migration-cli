<?php

namespace ByJG\DbMigration\Console;

use League\CLImate\CLImate;
use Exception;

class DownCommand extends ConsoleCommand
{
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
        try {
            if (!$this->confirmPartialMigrate($climate)) {
                $climate->out('Aborted.');
                return;
            }
            parent::execute($climate);
            $this->migration->down($this->upTo, true);
        } catch (Exception $ex) {
            $this->handleError($ex, $climate);
        }
    }
}
