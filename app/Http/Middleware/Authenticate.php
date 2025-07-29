<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            // Pour une API, retourne null pour éviter la redirection vers la route "login"
            abort(response()->json([
                'message' => 'Non authentifié.'
            ], 401));
        }

        return null;
    }
}