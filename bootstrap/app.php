<?php

use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api:      __DIR__ . '/../routes/api.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'api.auth' => ApiAuthMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (\Throwable $e, $request) {
            
            $statusCode = match (true) {
                $e instanceof NotFoundHttpException      => 404,
                $e instanceof AccessDeniedHttpException  => 403,
                $e instanceof AuthenticationException    => 401,
                $e instanceof ThrottleRequestsException  => 429,
                method_exists($e, 'getStatusCode')       => $e->getStatusCode(),
                default                                  => 500,
            };
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success'    => false,
                    'error_code' => 'SERVER_001',
                    'message'    => app()->isLocal()
                        ? $e->getMessage()
                        : 'An unexpected server error occurred.',
                ], $statusCode);
            }

            $map = [
                401 => [
                    'title'   => 'Unauthorized',
                    'message' => 'You must be authenticated to access this page.',
                ],
                403 => [
                    'title'   => 'Access Forbidden',
                    'message' => 'You do not have permission to access this resource.',
                ],
                404 => [
                    'title'   => 'Page Not Found',
                    'message' => 'The page you are looking for does not exist or has been moved.',
                ],
                429 => [
                    'title'   => 'Too Many Requests',
                    'message' => 'You have made too many requests. Please wait a moment and try again.',
                ],
                500 => [
                    'title'   => 'Something went wrong',
                    'message' => 'We encountered an unexpected error. Our team has been notified.',
                ],
            ];
            
            $info = $map[$statusCode] ?? $map[500];

            // ── Step 4: Custom error view return করো ─────────────
            return response()->view('errors.custom', [
                'statusCode' => $statusCode,
                'title'      => $info['title'],
                'message'    => $info['message'],
                'errorCode'  => class_basename($e),
                // Local env এ debug info দেখাও, production এ লুকাও
                'file'       => app()->isLocal() ? $e->getFile() : null,
                'line'       => app()->isLocal() ? $e->getLine() : null,
            ], $statusCode);
        });

    })->create();