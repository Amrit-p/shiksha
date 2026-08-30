<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
      'role' => RoleMiddleware::class,
      'permission' => PermissionMiddleware::class,
      'role_or_permission' => RoleOrPermissionMiddleware::class,
    ]);

    $middleware->redirectGuestsTo(function (Request $request) {
      if ($request->is('salesman') || $request->is('salesman/*')) {
        return route('salesman.login');
      }

      return route('login');
    });

    $middleware->redirectUsersTo(function (Request $request) {
      $user = $request->user();

      if ($user && method_exists($user, 'hasRole') && $user->hasRole('Sales Executive')) {
        return route('front.index');
      }

      return route('admin.dashboard');
    });
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    //
  })->create();
