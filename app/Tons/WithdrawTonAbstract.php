<?php

namespace App\Tons;

use Olifanton\Interop\Address;
use Olifanton\Interop\Units;
use Olifanton\Mnemonic\TonMnemonic;
use Olifanton\Ton\Contracts\Wallets\Transfer;
use Olifanton\Ton\Contracts\Wallets\TransferOptions;


abstract class WithdrawTonAbstract extends WithdrawAbstract
{
    public function process(string $mnemo, string $toAddress, string $tonAmount)
    {
        $transport = $this->getTransport();
        $words = explode(" ", trim($mnemo));
        $kp = TonMnemonic::mnemonicToKeyPair($words);
        $wallet = $this->getWallet($kp->publicKey);
        $extMsg = $wallet->createTransferMessage(
            [
                new Transfer(
                    new Address($toAddress),
                    Units::toNano($tonAmount)
                )
            ],
            new TransferOptions((int)$wallet->seqno($transport))
        );
        $transport->sendMessage($extMsg, $kp->secretKey);
    }
}


