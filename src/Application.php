<?php


namespace ByJG\DbMigration\Console;

use League\CLImate\CLImate;

class Application
{
    /**
     * @var Command[]
     */
    protected $commandList = [];

    protected $title;

    protected $version;

    /**
     * Application constructor.
     * @param $title
     * @param $version
     */
    public function __construct($title, $version)
    {
        $this->title = $title;
        $this->version = $version;
    }

    public function add(Command $command)
    {
        $this->commandList[$command->name()] = $command;
    }

    public function run()
    {
        global $argv, $argc;

        $climate = new CLImate();
        $climate->description($this->title);

        $fail = (count($argv) < 2);
        if ($fail || !isset($this->commandList[$argv[1]])) {
            $climate->out($this->title);
            $climate->out("");
            $climate->out("Usage: " . $argv[0] . " [command] [options]");
            $climate->out("");
            $climate->out("Command List:");
            foreach ($this->commandList as $command)  {
                $climate->out("  - " . $command->name() . ": " . $command->description());
            }
            $climate->out("");
            $climate->out("use --help to get more details about the command");
            $climate->out("");
            return;
        }

        $command = $argv[1];
        unset($argv[1]);
        $argv = array_values($argv);
        $argc--;

        $climate->arguments->add($this->commandList[$command]->arguments());

        try {
            $climate->arguments->parse();
        } catch (\Exception $ex) {
            if ($climate->arguments->get("help")) {
                $climate->usage();
                return;
            }

            $climate->error($ex->getMessage());

            if ($climate->arguments->get("verbose") >= 1) {
                $climate->out("");
                $climate->error("Stack Trace:");
                $climate->error($ex->getTraceAsString());
                $climate->out("");
            }
        }

    }
}
