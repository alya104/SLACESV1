<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // If not logged in, redirect to login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Debug: log the current user's role and allowed roles
        \Log::info('RoleMiddleware', [
            'user_role' => auth()->user()->role,
            'allowed_roles' => $roles
        ]);

        // If wrong role, show error
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}