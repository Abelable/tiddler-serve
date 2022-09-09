<?php

namespace App\Exceptions;


class BusinessException extends \Exception
{
    protected $statusCode;

    public function __construct(array $codeResponse, $tips = '', \Throwable $previous = null)
    {
        list($statusCode, $code, $message) = $codeResponse;
        $this->statusCode = $statusCode;
        parent::__construct(!empty($tips) ? $tips : $message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
