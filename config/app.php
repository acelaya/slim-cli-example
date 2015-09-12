<?php

// Generate the pathinfo by imploding the script arguments
array_shift($argv);
$pathinfo = implode(' ', $argv);

// Create our app instance
$app = new SlimController\Slim([
    'debug' => false, // Turn off Slim's own PrettyExceptions
    'controller.class_prefix'    => '',
    'controller.class_suffix'    => '',
    'controller.method_suffix'   => 'Action',
    'controller.template_suffix' => '',
]);

// Set up the environment so that Slim can route
$app->environment = Slim\Environment::mock([
    'PATH_INFO' => $pathinfo
]);

// CLI-compatible not found error handler
$app->notFound(function () use ($app) {
    $url = $app->environment['PATH_INFO'];
    echo "Error: Cannot route to $url" . PHP_EOL;
    $app->stop();
});

// Format errors for CLI
$app->error(function (\Exception $e) use ($app) {
    echo $e;
    echo PHP_EOL;
    $app->stop();
});

return $app;
