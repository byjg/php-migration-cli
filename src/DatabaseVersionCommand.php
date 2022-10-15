<?php

namespace ByJG\DbMigration\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class DatabaseVersionCommand extends ConsoleCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('version')
            ->setDescription('Get the current database version');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            parent::execute($input, $output);
            $versionInfo = $this->migration->getCurrentVersion();
            $output->writeln('version: ' . $versionInfo['version']);
            $output->writeln('status.: ' . $versionInfo['status']);
            return Command::SUCCESS;
        } catch (Exception $ex) {
            $this->handleError($ex, $output);
            return Command::FAILURE;
        }
        return 1;
    }
}
