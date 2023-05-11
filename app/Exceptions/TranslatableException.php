<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class TranslatableException extends Exception
{
    /**
     * Detailed and not user-friendly error message. Targeted to the developer.
     *
     * @var array<int|string,mixed>|string
     */
    protected array|string $error;

    /**
     * Determine if the exception should be reported.
     *
     * @var bool
     */
    protected bool $shouldReport;

    /**
     * Create a new exception instance.
     *
     * @param int                            $status
     * @param array<int|string,mixed>|string $error
     * @param string                         $message
     * @param null|Throwable                 $previous
     * @param bool                           $messageTranslatable
     * @param bool                           $shouldReport
     *
     * @return void
     */
    public function __construct(
        int $status,
        string|array $error,
        string $message,
        ?Throwable $previous = null,
        bool $messageTranslatable = true,
        bool $shouldReport = true
    ) {
        $this->error = $error;
        $this->shouldReport = $shouldReport;

        if ($messageTranslatable) {
            $message = strval(__($message));
        }

        parent::__construct($message, $status, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error'   => $this->error,
            'message' => $this->getMessage(),
        ], $this->getCode());
    }

    /**
     * Determine if the exception should use a custom logic for reporting.
     *
     * Notice that the method name is misleading, you'd the return value to
     * determine if the exception should be reported or not. But returning
     * false means there's no custom logic and the report will use the standard
     * report method.
     * More at https://laravel.com/docs/10.x/errors#renderable-exceptions
     *
     * @return bool
     */
    public function report(): bool
    {
        return !$this->shouldReport;
    }
}
