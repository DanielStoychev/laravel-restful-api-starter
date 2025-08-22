<?php

namespace App\Exceptions\Custom;

use Exception;

class UnauthorizedActionException extends Exception
{
    public function __construct($message = 'You are not authorized to perform this action', $code = 403)
    {
        parent::__construct($message, $code);
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'errors' => []
        ], $this->getCode());
    }
}
