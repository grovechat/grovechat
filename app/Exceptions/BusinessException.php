<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * 业务逻辑异常
 */
class BusinessException extends RuntimeException
{
    public function __construct(string $message, int $code = 200)
    {
        parent::__construct($message, $code);
    }
}
