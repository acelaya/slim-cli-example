<?php
use Acelaya\SlimCli\Controller\MainController;

$app->addControllerRoute('my-app:greeting', MainController::class . ':greeting')
    ->via('GET')
    ->name('greeting');

// Define the help command. If it doesn't have a name it won't include itself
$app->get('--help', function () use ($app) {
    echo 'Available commands:' . PHP_EOL;
    foreach ($app->router()->getNamedRoutes() as $route) {
        echo '    ' . $route->getPattern() . PHP_EOL;
    }
    echo PHP_EOL;
});
