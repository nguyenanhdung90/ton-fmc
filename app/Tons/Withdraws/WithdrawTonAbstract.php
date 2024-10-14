<?php

namespace App\Tons\Withdraws;

use Olifanton\Interop\Address;
use Olifanton\Interop\Units;
use Olifanton\Mnemonic\Exceptions\TonMnemonicException;
use Olifanton\Mnemonic\TonMnemonic;
use Olifanton\Ton\Contracts\Wallets\Transfer;
use Olifanton\Ton\Contracts\Wallets\TransferOptions;
use Olifanton\Ton\Exceptions\TransportException;


abstract class WithdrawTonAbstract extends WithdrawAbstract
{
    /**
     * @throws TransportException
     * @throws TonMnemonicException
     */
    public function process(string $toAddress, string $tonAmount, string $comment = "")
    {
        $phrases = config('services.ton.ton_mnemonic');
        $transport = $this->getTransport();
        $kp = TonMnemonic::mnemonicToKeyPair($phrases);
        $wallet = $this->getWallet($kp->publicKey);
        $extMsg = $wallet->createTransferMessage(
            [
                new Transfer(
                    dest: new Address($toAddress),
                    amount: Units::toNano($tonAmount),
                    payload: $comment,
                    sendMode: \Olifanton\Ton\SendMode::PAY_GAS_SEPARATELY,
                )
            ],
            new TransferOptions((int)$wallet->seqno($transport))
        );
        $transport->sendMessage($extMsg, $kp->secretKey);
    }
}


