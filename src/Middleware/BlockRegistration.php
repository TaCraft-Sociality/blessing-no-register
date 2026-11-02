<?php

declare(strict_types=1);

namespace Tacraft\TcsNoRegister\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * The registration page no longer exists. Old links (the login page used to
 * have one, the landing page still has two) are sent to the login page instead
 * of showing a dead page the player cannot use.
 */
class BlockRegistration
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET') && $request->is('auth/register')) {
            return redirect()->to(url('auth/login'));
        }

        return $next($request);
    }
}
