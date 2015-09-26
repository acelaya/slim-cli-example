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
                  ->describedAs('The client ID. Must be a number.')
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
        for ($i = 0; $i < 20; $i++) {
            echo '.';
        }
        echo PHP_EOL . 'Success!' . PHP_EOL;
    }
}
