<?php

namespace App\Tons;

use Http\Client\Common\HttpMethodsClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Olifanton\Interop\Address;
use Olifanton\Interop\Units;
use Olifanton\Mnemonic\TonMnemonic;
use Olifanton\Ton\Contracts\Wallets\Transfer;
use Olifanton\Ton\Contracts\Wallets\TransferOptions;
use Olifanton\Ton\Transports\Toncenter\ClientOptions;
use Olifanton\Ton\Transports\Toncenter\ToncenterHttpV2Client;
use Olifanton\Ton\Transports\Toncenter\ToncenterTransport;

abstract class WithdrawTonAbstract
{
    abstract public function getWallet($pubicKey);

    public function getBaseUri()
    {
        return config('services.ton.is_main_test') ? config('services.ton.base_uri_main') :
            config('services.ton.base_uri_test');
    }

    public function process(string $mnemo, string $toAddress, string $unit)
    {
        $baseUri = $this->getBaseUri();
        $httpClient = new HttpMethodsClient(
            Psr18ClientDiscovery::find(),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findStreamFactory(),
        );
        $tonCenter = new ToncenterHttpV2Client(
            $httpClient,
            new ClientOptions(
                $baseUri,
                config('services.ton.api_key')
            )
        );
        $transport = new ToncenterTransport($tonCenter);
        $words = explode(" ", trim($mnemo));
        $kp = TonMnemonic::mnemonicToKeyPair($words);
        $wallet = $this->getWallet($kp->publicKey);
        $extMsg = $wallet->createTransferMessage(
            [
                new Transfer(
                    new Address($toAddress),
                    Units::toNano($unit)
                )
            ],
            new TransferOptions((int)$wallet->seqno($transport))
        );
        $transport->sendMessage($extMsg, $kp->secretKey);
    }
}


