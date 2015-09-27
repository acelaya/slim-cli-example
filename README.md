# Slim CLI

How to use Slim framework to dipatch complex CLI requests

### Catching console requests

Slim does not support dispatching non-http requests (like console requests), so they need to be faked.
 
That can be easily done by creating a mock environment and overwriting Slim's one.

For example, let's assume this is our `bin/run` script.

```php
#!/usr/bin/env php
<?php
array_shift($argv); // Discard the filename
$pathinfo = array_shift($argv);
if (empty($pathinfo)) {
    $pathinfo = '--help';
}

$app = new Slim(...);
$app->environment = Slim\Environment::mock([
    'PATH_INFO' => $pathinfo
]);

// [...] Define help command and error management

$app->get('foo_bar', function () {
    echo 'Hello!!';
});

$app->run();
```

It catches the first argument and maps it to a route path, making Slim to think this is an HTTP request. If no argument is provided, it maps it to a `--help` path that will be used to display available commands.

So, if we want to run the foo_bar route, we will have tu execute this command.

```bash
bin/run foo_bar
```

It will print "Hello!!".

### Console helpers

That's fine, but it will be hard to manage complex arguments. We could define ordered route arguments, but we'll be forced to define them as string and provide them always in the same order.

To fix this, we are going to use the [nategood/commando](https://github.com/nategood/commando) package, that's a simple yet powerful CLI helper to manage arguments.

With it, we will be able to define flags and named params that doesn't need to be defined in a specific order, and also value validators.

On the other hand, we will need to be able to write formatted output, in order to provide feedback or print help instructions. For that purpose we are going to use the [phpleague/climate](http://climate.thephpleague.com/basic-usage/) package.

### Console actions

We will need every command to be mapped to an action, and the `Commando\Command` object to be properly configured for each case.

For that purpose, we can define controllers extending the `AbstractConsoleController`. It initializes the `Command` object to accept a first required command and defines an abstract `initCommand` method that will be called in order to customize the rest of the arguments.

Since every command will be different, we won't be able to define more than one action per controller, unless their arguments are exactly the same (or we set conditionals in the `initCommand` method implementation).

The `AbstractConsoleController` constructor also initializes the `CLImate` object.

### General help and command-specific help
