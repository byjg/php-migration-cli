<?php

namespace ByJG\DbMigration\Console;

use ByJG\DbMigration\Exception\DatabaseDoesNotRegistered;
use ByJG\DbMigration\Exception\DatabaseIsIncompleteException;
use ByJG\DbMigration\Exception\DatabaseNotVersionedException;
use ByJG\DbMigration\Exception\InvalidMigrationFile;
use ByJG\DbMigration\Exception\OldVersionSchemaException;
use ByJG\DbMigration\Exception\ResetDisabledException;
use League\CLImate\CLImate;

class ResetCommand extends ConsoleCommand
{
    public function name()
    {
        return 'reset';
    }

    public function description()
    {
        return '(Re)create a database doing all migrations';
    }

    /**
     * @param CLImate $climate
     * @return int|null|void
     * @throws ResetDisabledException
     * @throws DatabaseDoesNotRegistered
     * @throws DatabaseIsIncompleteException
     * @throws DatabaseNotVersionedException
     * @throws InvalidMigrationFile
     * @throws OldVersionSchemaException
     */
    public function execute(CLimate $climate)
    {
        if (getenv('MIGRATE_DISABLE_RESET') === "true") {
            throw new ResetDisabledException('Reset was disabled by MIGRATE_DISABLE_RESET environment variable. Cannot continue.');
        }

        if (!$this->yes) {
            $input = $climate->radio('This will ERASE all of data in your data. Continue with this action?', ['No', 'Yes']);
            $response = $input->prompt();
            if ($response == 'No') {
                $climate->out('Aborted.');
                return;
            }
        }

        parent::execute($climate);
        $this->migration->prepareEnvironment();
        $this->migration->reset($this->upTo);
    }
}
