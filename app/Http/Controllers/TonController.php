<?php

namespace App\Http\Controllers;

use App\Tons\Phrases\PhraseInterface;
use App\Tons\Withdraws\WithdrawTonV4R2Interface;
use App\Tons\Withdraws\WithdrawUSDTV4R2Interface;
use Illuminate\Http\Request;

class TonController extends Controller
{
    private WithdrawTonV4R2Interface $withdrawTon;

    private WithdrawUSDTV4R2Interface $withdrawUSDT;


    private PhraseInterface $phrase;

    public function __construct(
        WithdrawTonV4R2Interface $withdrawTon,
        WithdrawUSDTV4R2Interface $withdrawUSDT,
        PhraseInterface $phrase
    ) {
        $this->withdrawTon = $withdrawTon;
        $this->withdrawUSDT = $withdrawUSDT;
        $this->phrase = $phrase;
    }

    public function withdrawTON(Request $request): string
    {
        $phrases = $this->phrase->getPhrasesBy(1);
        $destinationAddress = '0QB2qumdPNrPUzgAAuTvG43NNBg45Cl4Bi_Gt81vE-EwF70k';
        $this->withdrawTon->process($phrases, $destinationAddress, "0.01", 'comment');
        return 'success';
    }

    public function withdrawUSDT(Request $request): string
    {
        $phrases = $this->phrase->getPhrasesBy(1);
        $destinationAddress = '0QB2qumdPNrPUzgAAuTvG43NNBg45Cl4Bi_Gt81vE-EwF70k';
        $this->withdrawUSDT->process($phrases, $destinationAddress, "0.2", 'comment');
        return 'success';
    }
}
