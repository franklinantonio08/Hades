<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidatePayment
{
    public function handle(Request $request, Closure $next)
    {
        // Validar que la solicitud viene de tu dominio
        $referer = $request->headers->get('referer');
        if (!str_contains($referer, config('app.url'))) {
            abort(403, 'Invalid request source');
        }

        // Validar CSRF token
        if (!$request->ajax() && !$request->wantsJson()) {
            if (!session()->token() === $request->input('_token')) {
                abort(419, 'Page Expired');
            }
        }

        return $next($request);
    }
}