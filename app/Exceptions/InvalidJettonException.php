<?php

namespace App\Exceptions;

use Exception;

class InvalidJettonException extends Exception
{
    public function render($request): \Illuminate\Http\JsonResponse
    {
        return response()->json(["error" => true, "message" => $this->getMessage()]);
    }
}
