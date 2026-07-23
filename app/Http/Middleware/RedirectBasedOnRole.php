<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $role = auth()->user()->getRoleNames()->first();

            $routes = [
                'mahasiswa'  => '/mahasiswa/dashboard',
                'reviewer'   => '/reviewer/dashboard',
                'panitia'    => '/panitia/dashboard',
                'admin'      => '/admin/dashboard',
                'management' => '/management/dashboard',
            ];

            if (isset($routes[$role])) {
                return redirect($routes[$role]);
            }
        }

        return $next($request);
    }
}
