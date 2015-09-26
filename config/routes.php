<?php
use Acelaya\SlimCli\Controller\GreetingController;
use Acelaya\SlimCli\Controller\ReportController;

$app->addControllerRoute('my-app/greeting', GreetingController::class . ':call')
    ->via('GET')
    ->name('greeting');

$app->addControllerRoute('my-app/gen-report', ReportController::class . ':call')
    ->via('GET')
    ->name('generate-report');

// Define the help command. If it doesn't have a name it won't include itself
$app->get('--help', function () use ($app) {
    echo 'Available commands:' . PHP_EOL;
    foreach ($app->router()->getNamedRoutes() as $route) {
        echo '    ' . $route->getPattern() . PHP_EOL;
    }
    echo PHP_EOL;
});
