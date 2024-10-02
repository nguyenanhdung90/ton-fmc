<?php

namespace App\Http\Controllers;

use App\Tons\DepositTonInterface;
use App\Tons\WithdrawTonV4R1Interface;
use App\Tons\WithdrawUSDTV4R2Interface;
use Illuminate\Http\Request;

class TonController extends Controller
{
    private $withdrawTon;

    private $withdrawUSDT;

    private $depositTon;

    public function __construct(
        WithdrawTonV4R1Interface $withdrawTon,
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

    public function withdraw(Request $request)
    {
        $mnemo = 'perfect ribbon dentist picture truth plunge crawl able velvet trip elite oyster census clog annual open note violin peasant gym bubble file gallery survey';
        $destinationAddress = '0QDt8nJuiKhM6kz99QjuB6XXVHZQZA350balZBMZoJiEDsVA';
        $this->withdrawTon->process($mnemo, $destinationAddress, "0.003");
        return 'success';
    }

    public function withdrawUSDT(Request $request)
    {
        $mnemo = 'perfect ribbon dentist picture truth plunge crawl able velvet trip elite oyster census clog annual open note violin peasant gym bubble file gallery survey';
        $destinationAddress = '0QB2qumdPNrPUzgAAuTvG43NNBg45Cl4Bi_Gt81vE-EwF70k';
        $this->withdrawUSDT->process($mnemo, $destinationAddress, "0.2");
        return 'success';
    }
}
