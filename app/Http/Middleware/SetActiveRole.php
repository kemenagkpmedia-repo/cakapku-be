<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetActiveRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $requestedRole = $request->header('X-Active-Role');

            if ($requestedRole) {
                $requestedRole = strtoupper($requestedRole);
                
                // Pastikan user benar-benar memiliki role tersebut
                if ($user->hasRole($requestedRole)) {
                    $user->active_role = $requestedRole;
                }
            }

            // Jika belum tereset (tidak ada header atau header tidak valid), 
            // biarkan model menghandle defaultnya saat dipanggil (lewat getFrontendConfig)
        }

        return $next($request);
    }
}
