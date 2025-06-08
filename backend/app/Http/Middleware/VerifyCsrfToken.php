<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // 'api/auth/*',
        // 'sanctum/csrf-cookie',
        // 'api/*',
        '*',
        'api/sync/*',
        'api/sync/products-with-categories'
    ];

    public function handle($request, Closure $next) {
        \Log::debug('CSRF Check', [
            'token' => $request->header('X-CSRF-TOKEN'),
            'session_token' => $request->session()->token(),
            'cookies' => $request->cookies->all()
        ]);
        
        return parent::handle($request, $next);
    }
} 