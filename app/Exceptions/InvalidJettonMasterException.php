<?php

namespace App\Exceptions;

use Exception;

class InvalidJettonMasterException extends Exception
{
    const INVALID_JETTON = '11';

    const NO_SUPPORT_JETTON = '12';

    public function render($request): \Illuminate\Http\JsonResponse
    {
        $code = (int)$this->getCode();
        $messageCode = $code < 10 ? '0' . $code : $code;
        return response()->json(["error" => true, "message" => $this->getMessage(), 'code' => $messageCode]);
    }
}
