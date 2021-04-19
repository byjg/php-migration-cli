<?php

namespace ByJG\DbMigration\Console;

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
        parent::execute($input, $output);
        try {
            $versionInfo = $this->migration->getCurrentVersion();
            $output->writeln('version: ' . $versionInfo['version']);
            $output->writeln('status.: ' . $versionInfo['status']);
            return 0;
        } catch (Exception $ex) {
            $this->handleError($ex, $output);
            return 1;
        }
    }
}
