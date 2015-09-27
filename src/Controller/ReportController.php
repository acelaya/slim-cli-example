<?php
namespace Acelaya\SlimCli\Controller;

class ReportController extends AbstractConsoleController
{
    /**
     * Initializes the command
     */
    public function initCommand()
    {
        $this->cliReader->option()
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
        $id = $this->cliReader['id'];
        $this->cliWriter->out(sprintf('Generating report for client with id %s', $id));
        for ($i = 0; $i < 20; $i++) {
            $this->cliWriter->inline('.');
        }
        $this->cliWriter->out('');
        $this->cliWriter->green()->bold()->out('Success!!');
    }
}
