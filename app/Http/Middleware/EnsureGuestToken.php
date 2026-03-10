<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuestToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $guestToken = $request->cookie('guest_token');

        $response = $next($request);

        if (!$guestToken) {
            $guestToken = Str::uuid()->toString();

            $response->cookie(
                'guest_token',
                $guestToken,
                60 * 24 * 15 // 30 días
            );
        }

        return $response;
    }
}

//Considerar para el futuro
//Hacer mas segura la cookie, no es urgente a dia de hoy 9 de marzo del 2026
/* $response->cookie(
    'guest_token',
    $guestToken,
    60 * 24 * 30,
    '/',
    null,
    true,   // https only
    false,
    false,
    'Lax'
); */