<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyEmbeddedSignature
{
    public function handle(Request $request, Closure $next)
    {
        $ts = $request->query('ts');
        $sig = $request->query('sig');

        if (!$ts || !$sig) {
            abort(403, 'Missing signature parameters');
        }

        $secret = 'ed2a55fb0653eb0bad1b6391cb907b4c3da9f8b8af40f8063910eff927e88c7d';
        // $secret = config('services.portal1.secret', 'ed2a55fb0653eb0bad1b6391cb907b4c3da9f8b8af40f8063910eff927e88c7d');

        if (!$secret) {
            Log::error('Portal secret not configured');
            abort(403, 'Configuration error');
        }

        $expectedSig = hash_hmac('sha256', $ts, $secret);

        if (!hash_equals($expectedSig, $sig)) {
            Log::warning('Invalid signature attempt', [
                'ts' => $ts,
                'provided_sig' => substr($sig, 0, 10) . '...',
                'expected_sig' => substr($expectedSig, 0, 10) . '...'
            ]);
            abort(403, 'Invalid signature');
        }

        if (abs(now()->timestamp - (int)$ts) > 120) {
            Log::warning('Expired signature attempt', [
                'ts' => $ts,
                'current_time' => now()->timestamp,
                'difference' => now()->timestamp - (int)$ts
            ]);
            abort(403, 'Signature expired!');
        }

        $existingUser = session('user');

        if ($existingUser) {
            
            if ($existingUser['role'] === 'admin') {
                Log::info('Keeping existing admin user in session', [
                    'user_name' => $existingUser['name'],
                    'role' => $existingUser['role'],
                    'email' => $existingUser['email']
                ]);
            } else {
                
                Log::info('Replacing non-admin user with embedded admin', [
                    'existing_role' => $existingUser['role'],
                    'existing_name' => $existingUser['name']
                ]);
                
                session(['user' => [
                    'role' => 'admin',
                    'zone' => 'All',
                    'br_code' => '',
                    'name' => $request->query('user_name', 'Embedded User'),
                    'email' => 'embedded@portal.com',
                    'updated_at' => now()->toDateTimeString(),
                    'created_at' => now()->toDateTimeString(),
                    'id' => 0 
                ]]);
            }
        } else {
            
            session(['user' => [
                'role' => 'admin',
                'zone' => 'All',
                'br_code' => '',
                'name' => $request->query('user_name', 'Embedded User'),
                'email' => 'embedded@portal.com',
                'updated_at' => now()->toDateTimeString(),
                'created_at' => now()->toDateTimeString(),
                'id' => 0 
            ]]);

            Log::info('Embedded access granted - new session', [
                'user_name' => $request->query('user_name', 'Embedded User'),
                'timestamp' => $ts
            ]);
        }

        return $next($request);
    }
}