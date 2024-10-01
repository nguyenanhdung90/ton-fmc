<?php

namespace App\Http\Controllers;

use App\Tons\WithdrawTonInterface;
use App\Tons\WithdrawV4R1Interface;
use Illuminate\Http\Request;

class TonController extends Controller
{
    private $withdrawTon;
    private $depositTon;

    public function __construct(WithdrawTonInterface $withdrawTon, WithdrawV4R1Interface $depositTon)
    {
        $this->withdrawTon = $withdrawTon;
        $this->depositTon = $depositTon;
    }


    public function deposit(Request $request)
    {
        $mnemo = "perfect ribbon dentist picture truth plunge crawl able velvet trip elite oyster census clog annual open note violin peasant gym bubble file gallery survey";
        return $this->withdrawTon->process($mnemo);
    }

    public function withdraw(Request $request)
    {
        $mnemo = 'perfect ribbon dentist picture truth plunge crawl able velvet trip elite oyster census clog annual open note violin peasant gym bubble file gallery survey';
        $destinationAddress = '0QDt8nJuiKhM6kz99QjuB6XXVHZQZA350balZBMZoJiEDsVA';
        $this->depositTon->process($mnemo, $destinationAddress, "0.003");
        return 'success';
    }
}
