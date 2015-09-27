<?php

// Generate the pathinfo by getting the first argument of the script
array_shift($argv); // Discard the filename
$pathinfo = array_shift($argv);
if (empty($pathinfo)) {
    $pathinfo = '--help';
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

$printCommands = function () use ($app) {
    echo 'Available commands:' . PHP_EOL;
    foreach ($app->router()->getNamedRoutes() as $route) {
        echo '    ' . $route->getPattern() . PHP_EOL;
    }
    echo PHP_EOL;
};
// Define the help command. If it doesn't have a name it won't include itself
$app->get('--help', $printCommands);
// CLI-compatible not found error handler
$app->notFound(function () use ($app, $printCommands) {
    $command = $app->environment['PATH_INFO'];
    echo sprintf('Error: Cannot route to command "%s"', $command) . PHP_EOL;
    $printCommands();
    $app->stop();
});

// Format errors for CLI
$app->error(function (\Exception $e) use ($app) {
    echo $e;
    echo PHP_EOL;
    $app->stop();
});

return $app;
