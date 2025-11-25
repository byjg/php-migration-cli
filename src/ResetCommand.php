<?php

namespace ByJG\DbMigration\Console;

use ByJG\DbMigration\Exception\ResetDisabledException;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ResetCommand extends ConsoleCommand
{
    #[\Override]
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('reset')
            ->setDescription('Create a fresh new database')
            ->addOption(
                'yes',
                null,
                InputOption::VALUE_NONE,
                'Answer yes to any interactive question'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ResetDisabledException
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (getenv('MIGRATE_DISABLE_RESET') === "true") {
            throw new ResetDisabledException('Reset was disabled by MIGRATE_DISABLE_RESET environment variable. Cannot continue.');
        }

        try {
            /** @var QuestionHelper $helper  */
            $helper = $this->getHelper('question');
            if (!$input->getOption('yes')) {
                $question = new ConfirmationQuestion(
                    'This will ERASE all of data in your data. Continue with this action? (y/N) ',
                    false
                );

                if (!$helper->ask($input, $output, $question)) {
                    $output->writeln('Aborted.');

                    return Command::FAILURE;
                }
            }

            parent::execute($input, $output);
            $this->migration->prepareEnvironment();
            $this->migration->reset($this->upTo);
            return Command::SUCCESS;
        } catch (Exception $ex) {
            $this->handleError($ex, $output);
            return Command::FAILURE;
        }
    }
}
