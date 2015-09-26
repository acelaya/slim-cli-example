<?php
namespace Acelaya\SlimCli\Controller;

class ReportController extends AbstractConsoleController
{
    /**
     * Initializes the command
     */
    public function initCommand()
    {
        $this->cmd->option()
                  ->alias('id')
                  ->describedAs('The client ID')
                  ->must(function ($argument) {
                      return is_numeric($argument);
                  })
                  ->require();
    }

    /**
     * This method is called at route dispatch
     */
    public function callAction()
    {
        $id = $this->cmd['id'];
        echo sprintf('Generating report for client with id %s', $id) . PHP_EOL;
    }
}
