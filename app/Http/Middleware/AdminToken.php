<?php

namespace App\Http\Middleware;

use Closure;

class AdminToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        $token = cache()->get('admin_token');
        $token = session()->get('admin_token');
        if (is_null($token)) {
            return redirect('/admin/auth/logout');
        } else {
            $res = java_get('/admin/user/check-token', ['token' => $token], []);
            if (!isset($res['statusCode']) || $res['statusCode'] != 0) {
                return redirect('/admin/auth/logout');
            }
            cache()->put('admin_token', $token, 60);
            session()->put('admin_token',$token);
        }
        return $next($request);
    }
}
