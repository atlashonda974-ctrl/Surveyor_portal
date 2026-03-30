<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
  public function handle(Request $request, Closure $next)
{
    $user = $request->session()->get('user');
    
    // To Check if this is an embedded request with signature
    if ($request->is('embedded/*') && $request->has(['ts', 'sig'])) {
        $ts = $request->query('ts');
        $sig = $request->query('sig');
        $secret = 'ed2a55fb0653eb0bad1b6391cb907b4c3da9f8b8af40f8063910eff927e88c7d';
        
        // Verify signature from both portals
        if ($secret && hash_equals(hash_hmac('sha256', $ts, $secret), $sig)) {
            if (abs(now()->timestamp - (int)$ts) <= 120) {
                // if the request is valid allw login if in vlaid require login
                return $next($request);
            }
        }
      
    }
    
 
    $currentPath = $request->path();

    $publicPaths = [
        'login',
        'forgetPass',
        'resetPassword'
    ];

    if (in_array($currentPath, $publicPaths) || 
        str_starts_with($currentPath, 'forgetPass/') ||
        str_starts_with($currentPath, 'resetPassword/')) {
        return $next($request);
    }
    

    // FOR ALREADY LOGGED IN USERS
    if ($request->is('login')) {
        if ($user) {
            return redirect($user['role'] === 'admin' ? '/admin/files' : '/');
        }
        return $next($request);
    }

    // NOT LOGGED IN USERS
    if (!$user) {
        return redirect('/login');
    }

    // TO RETAIN SURVEYOR FROM ADMIN PAGES
    if ($request->is('admin/*') && $user['role'] !== 'admin') {
        return redirect('/')->with('error', 'Access denied');
    }

    // TO RETAIN ADMIN FROM SURVEYOR
    if ($request->path() === '' || $request->path() === '/') {
        if ($user['role'] === 'admin') {
            return redirect('/admin/files')->with('error', 'Access denied');
        }
    }

    return $next($request);
}

}
