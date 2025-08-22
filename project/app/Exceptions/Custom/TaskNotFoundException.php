<?php

namespace App\Exceptions\Custom;

use Exception;

class TaskNotFoundException extends Exception
{
    public function __construct($message = 'Task not found', $code = 404)
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
