<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // Middleware yang berjalan pada semua request
    protected $middleware = [
        // Middleware lainnya...
    ];

    // Middleware groups untuk web dan API
    protected $middlewareGroups = [
        'web' => [
            // Middleware web lainnya...
        ],

        'api' => [
            // Middleware API lainnya...
        ],
    ];

    // Middleware route yang bisa diterapkan pada rute individu
    protected $routeMiddleware = [
        // Middleware lainnya
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'employee' => \App\Http\Middleware\EmployeeMiddleware::class,
    ];
}
