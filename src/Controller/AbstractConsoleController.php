<?php
namespace Acelaya\SlimCli\Controller;

use Commando\Command;
use Slim\Route;
use Slim\Slim;
use SlimController\SlimController;

abstract class AbstractConsoleController extends SlimController implements ConsoleAwareInterface
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
        $validCommands = $this->getValidCommands();
        $this->cmd->option()
                  ->require()
                  ->describedAs('The command to execute')
                  ->must(function () use ($validCommands) {
                      return $validCommands;
                  });
        $this->initCommand();
    }

    private function getValidCommands()
    {
        $validCommands = [];
        /** @var Route $route */
        foreach ($this->app->router()->getNamedRoutes() as $route) {
            $validCommands[] = $route->getPattern();
        }

        return $validCommands;
    }
}
