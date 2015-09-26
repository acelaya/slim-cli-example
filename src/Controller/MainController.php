<?php
namespace Acelaya\SlimCli\Controller;

class MainController extends AbstractConsoleController
{
    /**
     * Initializes the command
     */
    public function initCommand()
    {
        $this->cmd->option('name')
                  ->describedAs('The name to be displayed in the greeting')
                  ->require();

        $this->cmd->option('uppercase')
                  ->aka('u')
                  ->describedAs('If present, it will display the greetings uppercased')
                  ->boolean();
    }

    public function greetingAction()
    {
        $pattern = 'Hello %s!!';
        $capitalized = $this->cmd['uppercase'];
        $greeting = sprintf($pattern, $this->cmd['name']);
        echo $capitalized ? strtoupper($greeting) : $greeting;
        echo PHP_EOL;
    }
}
