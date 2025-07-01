<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\{AdminAuth, PreventBackHistory, CheckPermission}; //Import your middleware

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    // Register your route middleware alias here

       $middleware->alias([
        'admin' => AdminAuth::class,
        'prevent-back-history' => PreventBackHistory::class, // Correct: use alias, not global
        'check.permission' => CheckPermission::class,
      ]);
  })
  ->withExceptions(function (Exceptions $exceptions) {
    //
  })->create();
