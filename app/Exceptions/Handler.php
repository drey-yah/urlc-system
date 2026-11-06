<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof \Illuminate\Session\TokenMismatchException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your session expired. Please refresh the page and try again.',
                    'csrf_token' => csrf_token(),
                ], 419);
            }

            return redirect()->back()
                ->withInput($request->except($this->dontFlash))
                ->with('error', 'Your session expired due to inactivity, but your form data has been preserved. Please try submitting again.');
        }

        return parent::render($request, $e);
    }
}
