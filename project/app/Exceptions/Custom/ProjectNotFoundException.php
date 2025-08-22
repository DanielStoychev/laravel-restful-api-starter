<?php

namespace App\Exceptions\Custom;

use Exception;

class ProjectNotFoundException extends Exception
{
    public function __construct($message = 'Project not found', $code = 404)
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
