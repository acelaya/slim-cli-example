<?php
namespace Acelaya\SlimCli\Controller;

class GreetingController extends AbstractConsoleController
{
    /**
     * Initializes the command
     */
    public function initCommand()
    {
        $this->cliReader->option('name')
                        ->describedAs('The name to be displayed in the greeting')
                        ->require();

        $this->cliReader->option('uppercase')
                        ->aka('u')
                        ->describedAs('If present, it will display the greetings uppercased')
                        ->boolean();
    }

    /**
     * This method is called at route dispatch
     */
    public function callAction()
    {
        $pattern = 'Hello %s!!';
        $capitalized = $this->cliReader['uppercase'];
        $greeting = sprintf($pattern, $this->cliReader['name']);

        $this->cliWriter->green()->out($capitalized ? strtoupper($greeting) : $greeting);
    }
}
