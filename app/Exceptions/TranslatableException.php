<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TranslatableException extends Exception
{
    /**
     * error .
     *
     * @var string
     */
    protected $error;

    /**
     * Create a new exception instance.
     *
     * @param int            $status
     * @param string         $error
     * @param string         $message
     * @param null|Throwable $previous
     * @param bool           $isMessageTranslatable
     *
     * @return void
     */
    public function __construct(int $status, string $error, string $message, ?Throwable $previous = null, $isMessageTranslatable = true)
    {
        $this->error = $error;

        if ($isMessageTranslatable) {
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
    public function render($request)
    {
        return response()->json([
            'error'   => $this->error,
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
