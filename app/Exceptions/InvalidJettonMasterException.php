<?php

namespace App\Exceptions;

use Exception;

class InvalidJettonMasterException extends Exception
{
    public function render($request): \Illuminate\Http\JsonResponse
    {
        return response()->json(["error" => true, "message" => $this->getMessage()]);
    }
}
