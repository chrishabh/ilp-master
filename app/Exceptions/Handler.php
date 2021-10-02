<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

     /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->wantsJson() && $exception instanceof ValidationException) {   //add Accept: application/json in request
            return $this->handleValidationException($exception);
        } else {
            $retval = parent::render($request, $exception);
        }

        $retval = parent::render($request, $exception);
        return $retval;     
    }
    private function handleValidationException(ValidationException $exception)
    {
   
        return  response()->apiException($exception, $exception->errors());
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {   
        if($request->expectsJson() || $request->header('Authorization')){
            $response = ['success'=> true,'message' => 'Unauthorised Token', 'code' => 401];
            return response()->json($response,401); 
        }
        return redirect()->guest('login');
    }

}
