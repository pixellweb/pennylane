<?php


namespace PixellWeb\Pennylane\app;


use Throwable;

class PennylaneException extends \Exception
{
    /**
     * ReferentielApiException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        \Log::channel(config('pennylane.logging_channel'))->alert($message);
    }
}
