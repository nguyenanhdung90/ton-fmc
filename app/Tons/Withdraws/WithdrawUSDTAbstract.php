<?php

namespace App\Tons\Withdraws;

use Illuminate\Support\Facades\Log;
use Olifanton\Interop\Address;
use Olifanton\Interop\Boc\SnakeString;
use Olifanton\Interop\Units;
use Olifanton\Mnemonic\TonMnemonic;
use Olifanton\Ton\Contracts\Jetton\JettonMinter;
use Olifanton\Ton\Contracts\Jetton\JettonWallet;
use Olifanton\Ton\Contracts\Jetton\JettonWalletOptions;
use Olifanton\Ton\Contracts\Jetton\TransferJettonOptions;
use Olifanton\Ton\Contracts\Wallets\Transfer;
use Olifanton\Ton\Contracts\Wallets\TransferOptions;
use Olifanton\Ton\SendMode;

abstract class WithdrawUSDTAbstract extends WithdrawAbstract
{
    protected function getRootUSDT()
    {
        return config('services.ton.is_main') ? config('services.ton.root_usdt_main') :
            config('services.ton.root_usdt_test');
    }

    public function process(string $mnemo, string $destAddress, string $usdtAmount, string $comment = "")
    {
        $words = explode(" ", trim($mnemo));
        $kp = TonMnemonic::mnemonicToKeyPair($words);
        $wallet = $this->getWallet($kp->publicKey);
        /** @var Address $walletAddress */
        $walletAddress = $wallet->getAddress();
        $transport = $this->getTransport();
        $usdtRoot = JettonMinter::fromAddress(
            $transport,
            new Address($this->getRootUSDT())
        );
        $usdtWalletAddress = $usdtRoot->getJettonWalletAddress($transport, $walletAddress);
        $usdtWallet = new JettonWallet(new JettonWalletOptions(
            address: $usdtWalletAddress
        ));
        $state = $transport->getState($usdtWalletAddress);
        Log::info('get state : ' . json_encode($state));
        $extMessage = $wallet->createTransferMessage([
            new Transfer(
                    dest: $usdtWalletAddress,
                    amount: Units::toNano("0.1"),
                    payload: $usdtWallet->createTransferBody(
                        new TransferJettonOptions(
                            jettonAmount: Units::toNano($usdtAmount, Units::USDt),
                            toAddress: new Address($destAddress),
                            responseAddress: $walletAddress,
                            forwardPayload: SnakeString::fromString($comment)->cell(true),
                            forwardAmount: Units::toNano("0.0000001")
                        )
                    ),
                    sendMode: SendMode::IGNORE_ERRORS->combine(SendMode::PAY_GAS_SEPARATELY,
                                                               SendMode::CARRY_ALL_REMAINING_INCOMING_VALUE),
                )],
            new TransferOptions(seqno: (int)$wallet->seqno($transport),)
        );
        $transport->sendMessage($extMessage, $kp->secretKey);
    }
}
