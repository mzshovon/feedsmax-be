<?php

namespace App\Exceptions;

use App\Services\LoggerService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use PDOException;
use Predis\PredisException;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'trace'
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // Only for 404, 500 and database connectivity exception
        if ($request->is('*') &&
            (
                $e instanceof MethodNotAllowedException ||
                $e instanceof MethodNotAllowedHttpException
            )

        ) {
            $response = [];
            // If the app is in debug mode
            if (config('app.debug')) {
                // Add the exception class name, message and stack trace to response
                $response['exception'] = get_class($e); // Reflection might be better here
            }
            $status = Response::HTTP_METHOD_NOT_ALLOWED;
            // Return a JSON response with the response array and status code
            return $this->error($e->getMessage(), $e->getTrace(), $status , $response);
        }
        elseif ($request->is('*') &&
            (
                $e instanceof NotFoundHttpException ||
                $e instanceof PDOException ||
                $e instanceof InternalErrorException
            )
        ) {
            $response = [];
            // If the app is in debug mode
            if (config('app.debug')) {
                // Add the exception class name, message and stack trace to response
                $response['exception'] = get_class($e); // Reflection might be better here
            }
            $status = $e instanceof NotFoundHttpException ? Response::HTTP_NOT_FOUND : Response::HTTP_INTERNAL_SERVER_ERROR;
            // Return a JSON response with the response array and status code
            return $this->error($e->getMessage(), $e->getTrace(), $status , $response);
        }

        //Logs will be stored here for any exception
        $loggerService = app(LoggerService::class);
        $loggerService->init();
        $loggerService->exception($e->getMessage(), $e->getTraceAsString());

        // Default to the parent class' implementation of handler
        return parent::render($request, $e);
    }

}
