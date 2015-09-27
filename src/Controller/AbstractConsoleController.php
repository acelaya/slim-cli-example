<?php
namespace Acelaya\SlimCli\Controller;

use Commando\Command;
use League\CLImate\CLImate;
use Slim\Route;
use Slim\Slim;
use SlimController\SlimController;

abstract class  AbstractConsoleController extends SlimController implements ConsoleAwareInterface
{
    /**
     * @var Command
     */
    protected $cliReader;
    /**
     * @var CLImate
     */
    protected $cliWriter;

    public function __construct(Slim $app, Command $command = null, CLImate $climate = null)
    {
        parent::__construct($app);
        $this->cliReader = $command ?: new Command();
        $this->cliWriter = $climate ?: new CLImate();

        // Define the first mandatory command
        $currentCommand = $this->app->router()->getCurrentRoute()->getPattern();
        $this->cliReader->option()
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
