<?php


namespace ByJG\DbMigration\Console;


use League\CLImate\CLImate;

abstract class Command
{
    abstract public function name();
    abstract public function description();
    abstract public function arguments();
    abstract public function initialize(CLImate $climate);
    abstract public function execute(CLImate $climate);
}