<?php

namespace ByJG\DbMigration\Console;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseVersionCommand extends ConsoleCommand
{
    #[\Override]
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('version')
            ->setDescription('Get the current database version');

    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
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
    }
}
