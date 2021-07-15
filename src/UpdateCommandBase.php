<?php

namespace ByJG\DbMigration\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Exception;

abstract class UpdateCommandBase extends ConsoleCommand
{
    abstract protected function callMigrate();

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $versionInfo = $this->migration->getCurrentVersion();
            if (strpos($versionInfo['status'], 'partial') !== false) {
                $helper = $this->getHelper('question');
                $question = new ConfirmationQuestion(
                    'The database was not fully updated and maybe be unstable. Did you really want migrate the version? (y/N) ',
                    false
                );

                if (!$helper->ask($input, $output, $question)) {
                    $output->writeln('Aborted.');

                    return Command::FAILURE;
                }
            }

            parent::execute($input, $output);
            $this->callMigrate();
            return Command::SUCCESS;
        } catch (Exception $ex) {
            $this->handleError($ex, $output);
            return Command::FAILURE;
        }
    }
}
