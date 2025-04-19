<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class NormalizedException extends Exception
{
    /**
     * User-friendly error message. Targeted to the end-user.
     *
     * @var string
     */
    protected string $friendlyMessage;

    /**
     * Determine if the exception should be reported.
     *
     * @var bool
     */
    protected bool $shouldReport;

    /**
     * Contextual information about the error.
     *
     * @var array<int|string, mixed>
     */
    protected array $context = [];

    /**
     * Create a new exception instance.
     *
     * @param int                      $status
     * @param string                   $error
     * @param string                   $message
     * @param null|Throwable           $previous
     * @param array<int|string, mixed> $context
     * @param bool                     $shouldReport
     *
     * @return void
     */
    public function __construct(
        int $status,
        string $error,
        string $message,
        ?Throwable $previous = null,
        array $context = [],
        bool $shouldReport = true
    ) {
        $this->shouldReport = $shouldReport;
        $this->friendlyMessage = $message;
        $this->context = $context;

        parent::__construct($error, $status, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return null|\Illuminate\Http\JsonResponse
     */
    public function render(Request $request): ?JsonResponse
    {
        if (!$request->expectsJson()) {
            return null;
        }

        return response()->json([
            'error'   => $this->getMessage(),
            'message' => $this->friendlyMessage,
        ], $this->getCode());
    }

    /**
     * Get the context of the error.
     *
     * @return array<int|string, mixed>
     */
    public function context(): array
    {
        return $this->context;
    }

    /**
     * Determine if the exception should use a custom logic for reporting.
     *
     * Notice that the method name might be misleading, you'd exepct the return
     * value to determine if the exception should be reported or not. But returning
     * false means there's no custom logic and the report will use the standard
     * report method.
     * More at https://laravel.com/docs/12.x/errors#renderable-exceptions
     *
     * @return bool
     */
    public function report(): bool
    {
        return !$this->shouldReport;
    }

    /**
     * Normalize an exception into a NormalizedException instance.
     *
     * @param Throwable $e
     *
     * @return self
     */
    public static function normalize(Throwable $e): self
    {
        return match (true) {
            $e instanceof AuthenticationException => new self(
                status: 401,
                error: 'Unauthenticated.',
                message: __('api.ERROR.AUTH.UNAUTHENTICATED'),
                previous: $e,
                shouldReport: false
            ),
            $e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException => new self(
                status: 403,
                error: 'This action is unauthorized.',
                message: __('api.ERROR.AUTH.UNAUTHORIZED'),
                previous: $e,
                shouldReport: false
            ),
            $e instanceof ModelNotFoundException => new self(
                status: 404,
                error: $e->getMessage(),
                message: __('api.ERROR.MODEL_NOT_FOUND'),
                previous: $e,
                shouldReport: false
            ),
            $e instanceof NotFoundHttpException => new self(
                status: $e->getStatusCode(),
                error: $e->getMessage(),
                message: __('api.ERROR.ROUTE_NOT_FOUND'),
                previous: $e,
                shouldReport: false
            ),
            $e instanceof ValidationException => new self(
                status: $e->status,
                error: collect($e->errors())->flatten()->implode(' '),
                message: __($e->getMessage()),
                previous: $e,
                shouldReport: false,
            ),
            $e instanceof HttpException && App::isDownForMaintenance() => new self(
                status: $e->getStatusCode(),
                error: 'Application under maintenance.',
                message: __('api.ERROR.MAINTENANCE'),
                previous: $e,
                shouldReport: false,
            ),
            default => new self(
                status: method_exists($e, 'getStatusCode') && is_int($e->getStatusCode()) ? $e->getStatusCode() : 500,
                error: config('app.debug') && !App::runningUnitTests()
                    ? get_class($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage()
                    : $e->getMessage(),
                message: __('api.ERROR.SOMETHING_WENT_WRONG'),
                previous: $e,
                shouldReport: true
            ),
        };
    }
}
