<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     * Untuk semua route API, selalu kembalikan response JSON.
     */
    public function render($request, Throwable $e)
    {
        // Tangani khusus untuk semua request ke /api/*
        if ($request->is('api/*') || $request->expectsJson()) {

            // 401 - Tidak terautentikasi (token tidak ada / salah)
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'message' => 'Tidak terautentikasi. Harap login terlebih dahulu.',
                ], 401);
            }

            // 422 - Validasi gagal
            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => 'Data tidak valid.',
                    'errors'  => $e->errors(),
                ], 422);
            }

            // 404 - Model tidak ditemukan (findOrFail)
            if ($e instanceof ModelNotFoundException) {
                $model = class_basename($e->getModel());
                return response()->json([
                    'message' => "Data {$model} tidak ditemukan.",
                ], 404);
            }

            // 404 - Route tidak ditemukan
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => 'Endpoint tidak ditemukan.',
                ], 404);
            }

            // 405 - Method HTTP tidak diizinkan
            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'message' => 'Method HTTP tidak diizinkan untuk endpoint ini.',
                ], 405);
            }

            // 429 - Terlalu banyak request
            if ($e instanceof ThrottleRequestsException) {
                return response()->json([
                    'message' => 'Terlalu banyak request. Coba lagi beberapa saat.',
                ], 429);
            }

            // 500 - Server error lainnya
            return response()->json([
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error',
            ], 500);
        }

        return parent::render($request, $e);
    }
}
