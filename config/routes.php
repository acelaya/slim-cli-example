<?php
use Acelaya\SlimCli\Controller\MainController;

$app->addControllerRoute('greeting( --name=:name)( -v)', MainController::class . ':greeting')
    ->via('GET')
    ->name('greeting');
