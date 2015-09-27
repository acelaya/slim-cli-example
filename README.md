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

### Console helper

That's fine, but it will be hard to manage complex arguments. We could define ordered route arguments, but will be forced to define them as string and in certain order.

To fix this, we are going to use the [nategood/commando](https://github.com/nategood/commando) package, that's a simple yet powerful CLI helper.

With it, we will be able to define flags, command line helps, named params and value validators. Let's see how.

### Console actions

We will need every command to be maped to an action, and the `Commando\Command` object to be properly configured for each case.

