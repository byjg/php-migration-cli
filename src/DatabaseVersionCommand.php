<?php

namespace ByJG\DbMigration\Console;

use League\CLImate\CLImate;
use Exception;

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

    public function execute(CLimate $climate)
    {
        parent::execute($climate);
        $versionInfo = $this->migration->getCurrentVersion();
        $climate->out('version: ' . $versionInfo['version']);
        $climate->out('status.: ' . $versionInfo['status']);
    }
}
