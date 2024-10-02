<?php

namespace App\Http\Controllers;

use App\Tons\Deposits\DepositTonInterface;
use App\Tons\Withdraws\WithdrawTonV4R2Interface;
use App\Tons\Withdraws\WithdrawUSDTV4R2Interface;
use Illuminate\Http\Request;

class TonController extends Controller
{
    private $withdrawTon;

    private $withdrawUSDT;

    private $depositTon;

    public function __construct(
        WithdrawTonV4R2Interface $withdrawTon,
        DepositTonInterface $depositTon,
        WithdrawUSDTV4R2Interface $withdrawUSDT
    ) {
        $this->withdrawTon = $withdrawTon;
        $this->depositTon = $depositTon;
        $this->withdrawUSDT = $withdrawUSDT;
    }

    public function deposit(Request $request)
    {
        $userId = 123;
        return $this->depositTon->getBy($userId);
    }

    public function withdrawTON(Request $request)
    {
        $mnemo = 'perfect ribbon dentist picture truth plunge crawl able velvet trip elite oyster census clog annual open note violin peasant gym bubble file gallery survey';
        $destinationAddress = '0QB2qumdPNrPUzgAAuTvG43NNBg45Cl4Bi_Gt81vE-EwF70k';
        $this->withdrawTon->process($mnemo, $destinationAddress, "0.01", 'comment');
        return 'success';
    }

    public function withdrawUSDT(Request $request): string
    {
        $mnemo = 'perfect ribbon dentist picture truth plunge crawl able velvet trip elite oyster census clog annual open note violin peasant gym bubble file gallery survey';
        $destinationAddress = '0QB2qumdPNrPUzgAAuTvG43NNBg45Cl4Bi_Gt81vE-EwF70k';
        $this->withdrawUSDT->process($mnemo, $destinationAddress, "0.2", 'comment');
        return 'success';
    }
}
