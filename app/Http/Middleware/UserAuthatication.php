<?php

namespace App\Http\Middleware;

use App\Exceptions\AppException;
use App\Models\UserAuthorization;
use Closure;
use Illuminate\Http\Request;

class UserAuthatication
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
        // $path = $request->path();
        // $path = str_replace('api/','',$path);
        $token = $request->header('Authorization');//apache_request_headers();
        if(UserAuthorization::verifyToken($token))
        {
            return $next($request);
        } else {
            throw new AppException('Unauthorised Token',null,401,401);
        }
    }
}
