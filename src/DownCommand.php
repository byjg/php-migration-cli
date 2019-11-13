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
            $versionInfo = $this->migration->getCurrentVersion();
            if (strpos($versionInfo['status'], 'partial') !== false) {

                $input = $climate->radio('The database was not fully updated and maybe be unstable. Did you really want migrate the version?', ['No', 'Yes']);
                $response = $input->prompt();
                if ($response == 'No') {
                    $climate->out('Aborted.');

                    return;
                }
            }

            parent::execute($climate);
            $this->migration->down($this->upTo, true);
        } catch (Exception $ex) {
            $this->handleError($ex, $climate);
        }
    }
}
