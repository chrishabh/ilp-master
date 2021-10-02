<?php

namespace App\Http\Middleware;

use Closure;
use Mockery\Undefined;

class RequestPreProcessor
{
    /**
     * Handle an incoming request.
     * 
     * Parse Out the Request Object Body and Merge 
     * in main Request
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){

        if ($request->is('api/*')) {
            
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                return $next($request);
            }

            $request_data = $request->all();

            if(!empty($request_data['request'])){
                $request->merge((array)($request_data['request']));
                unset($request['request']);
            }

                return $next($request);
        }
        else{
            return response()->json(['success' => false,'code'=>500,'message'=>'system.message.4'], 500);    
        }
    }
}
