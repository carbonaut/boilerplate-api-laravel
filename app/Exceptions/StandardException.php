<?php

namespace App\Exceptions;

use Exception;

class StandardException extends Exception
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
     * @param \Illuminate\Contracts\Validation\Validator      $validator
     * @param null|\Symfony\Component\HttpFoundation\Response $response
     * @param string                                          $errorBag
     * @param null|mixed                                      $previous
     *
     * @return void
     */
    public function __construct(int $status, string $error, string $message, $previous = null)
    {
        $this->error = $error;

        parent::__construct($message, $status, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'error'   => $this->error,
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
