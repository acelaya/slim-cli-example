<?php
namespace Acelaya\SlimCli\Controller;

use Commando\Command;
use Slim\Route;
use Slim\Slim;
use SlimController\SlimController;

abstract class  AbstractConsoleController extends SlimController implements ConsoleAwareInterface
{
    /**
     * @var Command
     */
    protected $cmd;

    public function __construct(Slim $app, Command $command = null)
    {
        parent::__construct($app);
        $this->cmd = $command ?: new Command();

        // Define the first mandatory command
        $currentCommand = $this->app->router()->getCurrentRoute()->getPattern();
        $this->cmd->option()
                  ->require()
                  ->describedAs('The command to execute')
                  ->must(function ($command) use ($currentCommand) {
                      return $currentCommand === $command;
                  });
        $this->initCommand();
    }

    /**
     * This method is called at route dispatch
     */
    abstract public function callAction();
}
