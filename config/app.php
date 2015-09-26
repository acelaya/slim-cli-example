<?php

// Generate the pathinfo by getting the first argument of the script
array_shift($argv);
$pathinfo = array_shift($argv);
if (empty($pathinfo)) {
    $pathinfo = '--help';
} else {
    $pathinfo = implode('/', explode(':', $pathinfo));
}

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
    $command = $app->environment['PATH_INFO'];
    echo "Error: Cannot route to command $command" . PHP_EOL;
    $app->stop();
});

// Format errors for CLI
$app->error(function (\Exception $e) use ($app) {
    echo $e;
    echo PHP_EOL;
    $app->stop();
});

return $app;
