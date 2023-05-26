<?php

namespace App\Http\Middleware;

use App\Models\RequestResponseTracker as ModelsRequestResponseTracker;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class RequestResponseTracker
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
        $object = $next($request);

        if(isset($object->original)){
            $response = $object->original;
            $response_message   =   @$response['message'];
            $response_code=isset($response['code'])?$response['code']:'';
            $request_data = $request->all();
            $user_id    =   isset($request_data['user_id'])?$request_data['user_id']:null;
            $array  =   [
                'user_id' =>  $user_id,
                'request'   =>  json_encode($request->all()),
                'end_point' => $request->path(),
                'response'   => $response_message,
                'remote_address' => $request->ip(),
                'http_code'   => $object->getStatusCode(),
                'response_code' =>  $response_code,
                'type'   =>  $request->method(),
                'date' => Carbon::now(),
                'created_by'   => null,
                'updated_by' => null
            ];

            ModelsRequestResponseTracker::saveLogs($array);
        }
        return $object;
    }
}
