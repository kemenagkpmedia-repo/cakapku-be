<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Pure API project: selalu return null agar Laravel merespons dengan 401 JSON
        // bukan redirect ke route 'login' yang tidak ada
        return null;
    }
}
