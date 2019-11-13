<?php

namespace ByJG\DbMigration\Console;

use ByJG\DbMigration\Exception\ResetDisabledException;
use League\CLImate\CLImate;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Exception;

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
     * @throws \ByJG\DbMigration\Exception\DatabaseDoesNotRegistered
     * @throws \ByJG\DbMigration\Exception\DatabaseIsIncompleteException
     * @throws \ByJG\DbMigration\Exception\DatabaseNotVersionedException
     * @throws \ByJG\DbMigration\Exception\InvalidMigrationFile
     * @throws \ByJG\DbMigration\Exception\OldVersionSchemaException
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
