<?php
use Acelaya\SlimCli\Controller\GreetingController;
use Acelaya\SlimCli\Controller\ReportController;

$app->addControllerRoute('my-app_greeting', GreetingController::class . ':call')
    ->via('GET')
    ->name('greeting');

$app->addControllerRoute('my-app_gen-report', ReportController::class . ':call')
    ->via('GET')
    ->name('generate-report');
