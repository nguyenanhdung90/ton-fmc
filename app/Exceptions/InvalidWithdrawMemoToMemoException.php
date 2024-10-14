<?php

namespace App\Exceptions;

use Exception;

class InvalidWithdrawMemoToMemoException extends Exception
{
    const NONE_EXIST_SOURCE_MEMO = '21';
    const AMOUNT_SOURCE_MEMO_NOT_ENOUGH = '22';
    const NONE_EXIST_DESTINATION_MEMO = '23';
    const INVALID_AMOUNT = '24';

    public function render($request): \Illuminate\Http\JsonResponse
    {
        $code = (int)$this->getCode();
        $messageCode = $code < 10 ? '0' . $code : $code;
        return response()->json(["error" => true, "message" => $this->getMessage(), 'code' => $messageCode]);
    }
}
