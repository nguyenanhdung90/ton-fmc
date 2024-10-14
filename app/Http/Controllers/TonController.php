<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidWithdrawMemoToMemoException;
use App\Tons\Withdraws\WithdrawMemoToMemoInterface;
use App\Tons\Withdraws\WithdrawTonV4R2Interface;
use App\Tons\Withdraws\WithdrawUSDTV4R2Interface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TonController extends Controller
{
    private WithdrawTonV4R2Interface $withdrawTon;

    private WithdrawUSDTV4R2Interface $withdrawUSDT;

    private WithdrawMemoToMemoInterface $withdrawMemoToMemo;

    public function __construct(
        WithdrawTonV4R2Interface $withdrawTon,
        WithdrawUSDTV4R2Interface $withdrawUSDT,
        WithdrawMemoToMemoInterface $withdrawMemoToMemo
    ) {
        $this->withdrawTon = $withdrawTon;
        $this->withdrawUSDT = $withdrawUSDT;
        $this->withdrawMemoToMemo = $withdrawMemoToMemo;
    }

    public function withdrawTON(Request $request): string
    {
        $destinationAddress = '0QB2qumdPNrPUzgAAuTvG43NNBg45Cl4Bi_Gt81vE-EwF70k';
        $this->withdrawTon->process($destinationAddress, "0.01", 'comment');
        return 'success';
    }

    public function withdrawUSDT(Request $request): string
    {
        $destinationAddress = '0QB2qumdPNrPUzgAAuTvG43NNBg45Cl4Bi_Gt81vE-EwF70k';
        $this->withdrawUSDT->process($destinationAddress, "0.2", 'comment');
        return 'success';
    }

    public function withdrawOnlyMemo(Request $request): string
    {
        try {
            $this->withdrawMemoToMemo->transfer('https://t.me/testgiver_ton_bot', 'plus', 1, 'TON');
            return 'Success';
        } catch (\InvalidWithdrawMemoToMemoException $e) {
            Log::error('Error: ' . $e->getMessage());
        }
    }
}
