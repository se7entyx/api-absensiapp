<?php

use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckHRD;
use App\Http\Middleware\CheckProfile;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'check.profile' => CheckProfile::class,
            'check.admin' => CheckAdmin::class,
            'check.hrd' => CheckHRD::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
