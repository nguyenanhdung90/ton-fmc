<?php

namespace App\Tons;

use Http\Client\Common\HttpMethodsClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
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
use Olifanton\Ton\Transports\Toncenter\ClientOptions;
use Olifanton\Ton\Transports\Toncenter\ToncenterHttpV2Client;
use Olifanton\Ton\Transports\Toncenter\ToncenterTransport;
use Olifanton\Ton\SendMode;

abstract class WithdrawUSDTAbstract
{
    abstract public function getWallet($pubicKey);

    protected function getBaseUri()
    {
        return config('services.ton.is_main') ? config('services.ton.base_uri_main') :
            config('services.ton.base_uri_test');
    }

    protected function getTonApiKey()
    {
        return config('services.ton.is_main') ? config('services.ton.api_key_main') :
            config('services.ton.api_key_test');
    }

    protected function getRootUSDT()
    {
        return config('services.ton.is_main') ? config('services.ton.root_usdt_main') :
            config('services.ton.root_usdt_test');
    }

    protected function getTransport(): ToncenterTransport
    {
        $httpClient = new HttpMethodsClient(
            Psr18ClientDiscovery::find(),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findStreamFactory(),
        );
        $tonCenter = new ToncenterHttpV2Client(
            $httpClient,
            new ClientOptions(
                $this->getBaseUri(),
                $this->getTonApiKey()
            )
        );
        return new ToncenterTransport($tonCenter);
    }

    public function process(string $mnemo, string $destAddress, string $usdtAmount)
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
        $textComment = 'a';
        $extMessage = $wallet->createTransferMessage([
            new Transfer(
                dest: $usdtWalletAddress,
                amount: Units::toNano("0.1"),
                payload: $usdtWallet->createTransferBody(
                new TransferJettonOptions(
                    jettonAmount: Units::toNano($usdtAmount, Units::USDt),
                    toAddress: new Address($destAddress),
                    responseAddress: $walletAddress,
                    forwardPayload: SnakeString::fromString($textComment)->cell(true),
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
