<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        // Case-Specific handler
        if($exception instanceof ModelNotFoundException) {
            return response()->json([
                'code' => 404,
                'status' => false,
                'message' => 'Entry for ' . str_replace('App\\', '', $exception->getModel()) . ' not found'
            ], 404);
        }
        if($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'code' => 405,
                'status' => false,
                'message' => $exception->getMessage()
            ], 405);
        }
        if($exception instanceof AuthenticationException) {
            return response()->json([
                'code' => 401,
                'status' => false,
                'message' => 'Not authorized'
            ], 401);
        }

        // Other Http Exceptions
        if($this->isHttpException($exception)) {
            $error = $this->convertExceptionToResponse($exception);
            return response()->json([
                'code' => $error->getStatusCode(),
                'status' => false,
                'message' => ($exception->getMessage() == '') ? "Resource not Found" : $exception->getMessage()
            ], $exception->getStatusCode());
        }

        // If all above fails then use any one of the following:
        // return parent::render($request, $exception);
        if($exception) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    // protected function unauthenticated($request, AuthenticationException $exception)
    // {
    //     return response()->json(['error' => 'Unauthenticated.'], 401);
    // }

}