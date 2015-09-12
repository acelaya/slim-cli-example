<?php
namespace Acelaya\SlimCli\Controller;

class MainController extends AbstractControleController
{
    public function greetingAction()
    {
        $name = $this->getCliParam('name', 'unknown person');
        $verbose = $this->getCliFlag('verbose');

        echo sprintf('Hello %s!!', $name) . PHP_EOL;
        if ($verbose) {
            echo 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.' . PHP_EOL;
        }
    }
}
