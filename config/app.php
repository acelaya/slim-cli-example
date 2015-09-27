<?php

use League\CLImate\CLImate;

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

// Define the help command. If it doesn't have a name it won't include itself
$app->get('--help', function () use ($app) {
    $writer = new CLImate();
    $writer->bold()->out('Available commands:');
    foreach ($app->router()->getNamedRoutes() as $route) {
        $writer->green()->out('    ' . $route->getPattern());
    }
});
// CLI-compatible not found error handler
$app->notFound(function () use ($app) {
    $writer = new CLImate();
    $command = $app->environment['PATH_INFO'];
    $writer->red()->bold()->out(sprintf('Error: Cannot route to command "%s"', $command));
    $helpRoute = $app->router()->getMatchedRoutes('GET', '--help', true);
    $helpRoute[0]->dispatch();
    $app->stop();
});

// Format errors for CLI
$app->error(function (\Exception $e) use ($app) {
    echo $e;
    echo PHP_EOL;
    $app->stop();
});

return $app;
