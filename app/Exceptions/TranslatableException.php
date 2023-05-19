<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class TranslatableException extends Exception
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
     * @param bool                     $messageTranslatable
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
        bool $messageTranslatable = true,
        array $context = [],
        bool $shouldReport = true
    ) {
        $this->shouldReport = $shouldReport;
        $this->friendlyMessage = $messageTranslatable ? strval(__($message)) : $message;
        $this->context = $context;

        parent::__construct($error, $status, $previous);
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
