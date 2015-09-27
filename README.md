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

We will need every command to be mapped to an action, and the `Commando\Command` object to be properly configured for each specific case.

For that purpose, we can define controllers extending the `AbstractConsoleController`. It initializes the `Command` object to accept a first required command and defines an abstract `initCommand` method that will be called in order to customize the rest of the arguments.

Since every command will be different, we won't be able to define more than one action per controller, unless their arguments are exactly the same (or we set conditionals in the `initCommand` method implementation).

The `AbstractConsoleController` constructor also initializes the `CLImate` object. This is how it looks like.

```php
public function __construct(Slim $app, Command $command = null, CLImate $climate = null)
{
    parent::__construct($app);
    $this->cliReader = $command ?: new Command();
    $this->cliWriter = $climate ?: new CLImate();

    // Define the first mandatory command
    $currentCommand = $this->app->router()->getCurrentRoute()->getPattern();
    $this->cliReader->option()
                    ->require()
                    ->describedAs('The command to execute')
                    ->must(function ($command) use ($currentCommand) {
                        return $currentCommand === $command;
                    });
    $this->initCommand();
}
```

And a concrete implementation, like the `GreetingController`, could look like this

```php
/**
 * Initializes the command
 */
public function initCommand()
{
    $this->cliReader->option('name')
                    ->describedAs('The name to be displayed in the greeting')
                    ->require();

    $this->cliReader->option('uppercase')
                    ->aka('u')
                    ->describedAs('If present, it will display the greetings in uppercase')
                    ->boolean();
}

/**
 * This method is called at route dispatch
 */
public function callAction()
{
    $pattern = 'Hello %s!!';
    $capitalized = $this->cliReader['uppercase'];
    $greeting = sprintf($pattern, $this->cliReader['name']);

    $this->cliWriter->green()->out($capitalized ? strtoupper($greeting) : $greeting);
}
```

We could register this command as a Slim route like this:

```php
$app->addControllerRoute('my-app_greeting', GreetingController::class . ':call')
    ->via('GET')
    ->name('greeting');
```

And finally, this action would be dispatched by running any of these commands.

```bash
> bin/run my-app_greeting --name "Alejandro Celaya"
# This would print "Hello Alejandro Celaya!!"

> bin/run my-app_greeting --name "Alejandro Celaya" --uppercase
# This would print "HELLO ALEJANDRO CELAYA!!"

> bin/run my-app_greeting --name "Alejandro Celaya" -u
# This would print "HELLO ALEJANDRO CELAYA!!"

> bin/run my-app_greeting --uppercase --name "Alejandro Celaya"
# This would print "HELLO ALEJANDRO CELAYA!!"
```

### General help and command-specific help

Since it is hard to remember all the available commands and their signature, it is very usefull to have "help" commands.

The `Command` class comes with a built in `--help` param that displays a human-friendly help for certain command.

So, if we want to see the *greeting* command help, we just need to run this:

```bash
> bin/run my-app_greeting --help
# This would print a nice human-friendly help
```

But we still need a way to get the list of available commands.

We can take advantage of Slim's error management, and use the notFound error to display the available commands, and also define a --help command that does the same.

```php
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
    
    // Dispatching the "help" route will print the available commands in addition to the error
    $helpRoute = $app->router()->getMatchedRoutes('GET', '--help', true);
    $helpRoute[0]->dispatch();
    
    $app->stop();
});
```

Once this is configured we can run any of these commands to display the help.

```bash
> bin/run --help
# Will display the list of valid commands

> bin/run
# Will display the list of valid commands too, since we configured that any empty command is mapped to the --help command

> bin/run something_invalid
# Will display an error because this is an invalid command
# Then, it will display thelist of valid commands
```

### Best practices

We have to follow some kind of convention in order to prevent duplicated commands.

The symfony and doctrine people usually recommend to namespace commands by using the colon character, this way instead of having the `greeting` command, which is too common and easy to be duplicated, they prefix some kind of vendor name followed by a colon, `package:greeting`.

This is not possible with Slim, since the commands are used as route patterns, and Slim uses a regular expressions that does weird things when the pattern contains colons.

Instead we can use underscores, like in the examples, `my-app_greeting`.

### Testing this in a real environment

This project is a real application where you can test what we have just explained.

Clone this repository, run `composer install` to get everything installed and run the command `bin/run` to play with the available commands.
