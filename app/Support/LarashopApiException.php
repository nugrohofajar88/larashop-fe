<?php

namespace App\Support;

use RuntimeException;

class LarashopApiException extends RuntimeException
{
    public function __construct(
        public readonly int $status,
        string $message,
        public readonly array $errors = [],
    ) {
        parent::__construct($message, $status);
    }
}
