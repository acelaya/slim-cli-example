# Slim CLI

How to use Slim framework to dipatch complex CLI requests

### Catching console requests

Slim does not support dispatching non-http requests (like console requests), so they need to be faked.
 
That can be easily done by creating a mock environment and overwriting Slim's one.

For example, let's assume this is our `bin/run` script.

```php
#!/usr/bin/env php
<?php
array_shift($argv);
$pathinfo = array_shift($argv);
if (empty($pathinfo)) {
    $pathinfo = '--help';
} else {
    $pathinfo = implode('/', explode(':', $pathinfo));
}

$app = new Slim(...);
$app->environment = Slim\Environment::mock([
    'PATH_INFO' => $pathinfo
]);
```

It catches the first argument and maps it to a route path, making Slim to think this is an HTTP request. If no argument is provided, it maps it to a `--help` path that will be used to display available commands.

### Console helper
